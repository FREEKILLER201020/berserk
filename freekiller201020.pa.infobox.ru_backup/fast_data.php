<html>
	<head>
		<style>
			/* label, a {
					font-family : Arial, Helvetica, sans-serif;
					font-size : 12px;
			} */
			table, th, td {
    		border: 1px solid black;
				margin-right: 2%;
				/* border-collapse: collapse; */
			}
			td,th {
    		/* border: none; */
			}
			br {
  			content: "";
  			margin: 1em;
  			display: block;
  			font-size: 15%;
			}
			a {
color:#000000;
text-decoration:none
}
a:active {
color:#000000;
text-decoration:none
}
a:visited {
color:#000000;
text-decoration:none
}
a:hover {
color:#000000;
text-decoration: none
}
* { font-family:'Arial';
font-size: normal;}
		</style>
		<script src="jquery.js"></script>
		<script src="jquery-ui.js"></script>
		<link href="jquery-ui.css" rel="stylesheet">
	</head>

	<body>
		<form action="<?php echo htmlentities($_SERVER['PHP_SELF'])?>" method="post">
<?php
require("classes.php");
require("data.php");
        $file  = file_get_contents(realpath(dirname(__FILE__))."/../config.json");
$config = json_decode($file, true);
$connection=Connect($config);



// $date=null;
// if (isset($_POST['formSubmit'])) {
// 		$date= $_POST['date'];
// }
// $dates=array();
// $query = "\nSELECT DISTINCT timemark FROM {$config["base_database"]}.Players ORDER BY timemark DESC;\n";
// $result = $connection->query($query);
// if ($result->num_rows > 0) {
// while ($row = $result->fetch_assoc()) {
// 		$tmp=array();
// 		$split=explode("-", $row["timemark"]);
// 		$split2=explode(" ", $split[2]);
// 		$year=(int)$split[0];
// 		$month=(int)$split[1];
// 		$day=(int)$split2[0];
// 		$ret="$split[1]-$split2[0]-$split[0]";
// 		$ret2="$day-$month-$year";
// 		array_push($tmp, $ret);
// 		array_push($tmp, $ret2);
// 		array_push($dates, $tmp);
// }
// }



$clans=array();
$query = "\nSELECT * FROM {$config["base_database"]}.Attacks_fast ORDER BY resolved asc\n";
// echo $query;
$result = $connection->query($query);
// print_r($result);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // print_r($row);

        array_push($clans, $row["attacker"]);
        array_push($clans, $row["defender"]);
    }
}
// print_r($clans);
$clans=array_unique($clans, SORT_REGULAR);
// print_r($clans);
$clan_selected=171;
if (isset($_POST['formSubmit'])) {
    $clan_selected = $_POST['Clan'];
}

echo "<br>	Клан: <select name=\"Clan\">
						<option value=\"-1\"> Все кланы </option>
				";
foreach ($clans as $clan) {
    $name=GetClanName2($connection, $config, $clan);
    if ($clan==$clan_selected) {
        echo "<option selected value=\"$clan\">$name</option>";
    } else {
        echo "<option value=\"$clan\">$name</option>";
    }
}

echo " </select>";
echo "<br><input type=\"submit\" name=\"formSubmit\" value=\" Обновить \" /><br>";

$attacks=array();
$query = "\nSELECT * FROM {$config["base_database"]}.Attacks_fast ORDER BY resolved asc\n";
// echo $query;
$result = $connection->query($query);
// print_r($result);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // print_r($row);
        if ($clan_selected!=-1) {
            if (($row["attacker"]==$clan_selected)||($row["defender"]==$clan_selected)) {
                $tmp=array();
                array_push($tmp, $row["fromm"]);
                array_push($tmp, $row["too"]);
                array_push($tmp, $row["attacker"]);
                array_push($tmp, $row["defender"]);
                array_push($tmp, $row["declared"]);
                array_push($tmp, $row["resolved"]);
                array_push($tmp, $row["ended"]);
                array_push($attacks, $tmp);
            }
        } else {
            $tmp=array();
            array_push($tmp, $row["fromm"]);
            array_push($tmp, $row["too"]);
            array_push($tmp, $row["attacker"]);
            array_push($tmp, $row["defender"]);
            array_push($tmp, $row["declared"]);
            array_push($tmp, $row["resolved"]);
            array_push($tmp, $row["ended"]);
            array_push($attacks, $tmp);
        }
    }
}



echo "<table style=\"float: left\">
<th align=\"center\"> № </th>
<th align=\"center\"> From </th>
<th align=\"center\"> To </th>
<th align=\"center\"> Attacker </th>
<th align=\"center\"> Defender </th>
<th align=\"center\"> Declared </th>
<th align=\"center\"> Resolved </th>
<th align=\"center\"> Ended </th>
</tr>";
$cnt=1;
foreach ($attacks as $attack) {
    // foreach ($players2 as $pl2) {
    //     if ($pl1[0]==$pl2[0]) {
    // echo " $pl1[0]==$pl2[0]";
    echo "<tr>";
    // echo "<td>{$pl1[1]}</td>";
    echo "<td align=\"center\">$cnt</td>";
    $cnt++;
    echo "<td>{$attack[0]}</td>";
    echo "<td>{$attack[1]}</td>";
    $name1=GetClanName2($connection, $config, $attack[2]);
    $name2=GetClanName2($connection, $config, $attack[3]);
    echo "<td>{$name1}</td>";
    echo "<td>{$name2}</td>";
    $dt1=CorrectDate($attack[4], 3*60*60);
    $dt2=CorrectDate($attack[5], 3*60*60);
    echo "<td>$dt1</td>";
    echo "<td>$dt2</td>";
    if ($attack[6]==null) {
        echo "<td>NULL</td>";
    } else {
        // echo "<td>{$attack[6]}</td>";
        $dt3=CorrectDate($attack[6], 3*60*60);
        echo "<td><p><a href=\"/fast_data2.php?from={$attack[0]}&to={$attack[1]}&attacker={$attack[2]}&defender={$attack[3]}&declared={$attack[4]}&resolved={$attack[5]}&end={$attack[6]}&clan_selected=$clan_selected\">$dt3</a></p></td>";
    }
    echo "</tr>";
    //     }
    // }
}
echo "</table>";


function Connect($config) // Функция подключения к БД
{
    $connection = new mysqli($config["hostname"].$config["port"], $config["username"], $config["password"]);
    if ($connection->connect_errno) {
        die("Unable to connect to MySQL server:".$connection->connect_errno.$connection->connect_error);
    }
    // Установка параметров соединения (не уверен, что это надо)
    $connection->query("SET NAMES 'utf8'");
    $connection->query("SET CHARACTER SET 'utf8'");
    $connection->query("SET SESSION collation_connection = 'utf8_general_ci'");
    if ($connection && $config["debug"]) {
        // echo("Connected to MySQL server.\n");
    }
    return $connection;
}

function GetLatestDate($connection, $config)
{
    $query = "SELECT timemark FROM {$config["base_database"]}.Players ORDER BY timemark DESC LIMIT 1;\n";
    // echo $query;
    $result = $connection->query($query);
    // print_r($result);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key=> $data) {
                // echo "$key => $data";
                if ($key=="timemark") {
                    return $data;
                }
            }
        }
    } else {
        return -1;
    }
}

function GetFirstDate($connection, $config)
{
    $query = "SELECT timemark FROM {$config["base_database"]}.Players ORDER BY timemark ASC LIMIT 1;\n";
    // echo $query;
    $result = $connection->query($query);
    // print_r($result);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key=> $data) {
                // echo "$key => $data";
                if ($key=="timemark") {
                    return $data;
                }
            }
        }
    } else {
        return -1;
    }
}

function GetClanName($connection, $config, $id, $time)
{
    $query = "SELECT * FROM {$config["base_database"]}.Clans WHERE id=$id and timemark=\"$time\";\n";
    // echo $query;
    $result = $connection->query($query);
    // print_r($result);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key=> $data) {
                // echo "$key => $data";
                if ($key=="title") {
                    return $data;
                }
            }
        }
    } else {
        return -1;
    }
}

function CheckDatee($connection, $config, $time)
{
    $query = "SELECT * FROM {$config["base_database"]}.Players WHERE timemark=\"$time\";\n";
    // echo $query;
    $result = $connection->query($query);
    // print_r($result);
    if ($result->num_rows > 0) {
        return 1;
    } else {
        return -1;
    }
}

function GetClanName2($connection, $config, $id)
{
    $query = "SELECT * FROM {$config["base_database"]}.Clans_fast WHERE id=$id order by timemark desc limit 1;\n";
    // echo $query;
    $result = $connection->query($query);
    // print_r($result);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key=> $data) {
                // echo "$key => $data";
                if ($key=="title") {
                    return $data;
                }
            }
        }
    } else {
        return -1;
    }
}
function CorrectDate($date, $correction)
{
    return $date=date("Y-m-d H:i:s", strtotime($date)+$correction);
}
?>

	</body>
