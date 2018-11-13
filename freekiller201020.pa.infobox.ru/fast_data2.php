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

$from=$_GET['from'];
if (isset($_POST['formSubmit'])) {
    $from = $_POST['from'];
}
echo "<input type=\"hidden\" name=\"from\" value=\"$from\">";
$to=$_GET['to'];
if (isset($_POST['formSubmit'])) {
    $to = $_POST['to'];
}
echo "<input type=\"hidden\" name=\"to\" value=\"$to\">";
$attacker=$_GET['attacker'];
if (isset($_POST['formSubmit'])) {
    $attacker = $_POST['attacker'];
}
echo "<input type=\"hidden\" name=\"attacker\" value=\"$attacker\">";
$defender=$_GET['defender'];
if (isset($_POST['defender'])) {
    $defender = $_POST['defender'];
}
echo "<input type=\"hidden\" name=\"defender\" value=\"$defender\">";
$declared=$_GET['declared'];
if (isset($_POST['formSubmit'])) {
    $declared = $_POST['declared'];
}
echo "<input type=\"hidden\" name=\"declared\" value=\"$declared\">";
$resolved=$_GET['resolved'];
if (isset($_POST['formSubmit'])) {
    $resolved = $_POST['resolved'];
}
echo "<input type=\"hidden\" name=\"resolved\" value=\"$resolved\">";
$end=$_GET['end'];
if (isset($_POST['formSubmit'])) {
    $end = $_POST['end'];
}
echo "<input type=\"hidden\" name=\"end\" value=\"$end\">";
$clan_selected=$_GET['clan_selected'];
$clans=array();
array_push($clans, $attacker);
array_push($clans, $defender);

if (isset($_POST['formSubmit'])) {
    $clan_selected = $_POST['Clan'];
}

// echo $from,$to,$attacker,$defender,$declared,$resolved,$end,$clan_selected;

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
// echo "<form  action=\"fast_data.php\"> <button type=\"submit\">Continue</button> </form>";




// SELECT * FROM `Players_fast` WHERE timemark=(Select timemark FROM `Players_fast` WHERE timemark>="2018-09-15 10:38:24" limit 1)



$plstart=array();
$query = "\nSELECT * FROM {$config["base_database"]}.Players_fast WHERE timemark=(Select timemark FROM {$config["base_database"]}.Players_fast WHERE timemark<=\"$resolved\" ORDER BY timemark DESC limit 1) ORDER BY nick ASC\n";
// echo $query;
$result = $connection->query($query);
// print_r($result);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // print_r($row);
        if ($clan_selected!=-1) {
            if ($row["clan_id"]==$clan_selected) {
                // if (($row["clan_id"]==$attacker)||($row["clan_id"]==$defender)) {
                $tmp=array();
                array_push($tmp, $row["id"]);
                array_push($tmp, $row["nick"]);
                array_push($tmp, $row["frags"]);
                array_push($tmp, $row["deaths"]);
                array_push($tmp, $row["level"]);
                array_push($tmp, $row["clan_id"]);
                array_push($plstart, $tmp);
            }
        } else {
            if (($row["clan_id"]==$attacker)||($row["clan_id"]==$defender)) {
                $tmp=array();
                array_push($tmp, $row["id"]);
                array_push($tmp, $row["nick"]);
                array_push($tmp, $row["frags"]);
                array_push($tmp, $row["deaths"]);
                array_push($tmp, $row["level"]);
                array_push($tmp, $row["clan_id"]);
                array_push($plstart, $tmp);
            }
        }
    }
}

$plend=array();
$query = "\nSELECT * FROM {$config["base_database"]}.Players_fast WHERE timemark=(Select timemark FROM {$config["base_database"]}.Players_fast WHERE timemark>=\"$end\" ORDER BY timemark ASC limit 1) ORDER BY nick ASC\n";
// echo $query;
$result = $connection->query($query);
// print_r($result);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // print_r($row);
        if ($clan_selected!=-1) {
            if ($row["clan_id"]==$clan_selected) {
                // if (($row["clan_id"]==$attacker)||($row["clan_id"]==$defender)) {
                $tmp=array();
                array_push($tmp, $row["id"]);
                array_push($tmp, $row["nick"]);
                array_push($tmp, $row["frags"]);
                array_push($tmp, $row["deaths"]);
                array_push($tmp, $row["level"]);
                array_push($tmp, $row["clan_id"]);
                array_push($plend, $tmp);
            }
        } else {
            if (($row["clan_id"]==$attacker)||($row["clan_id"]==$defender)) {
                $tmp=array();
                array_push($tmp, $row["id"]);
                array_push($tmp, $row["nick"]);
                array_push($tmp, $row["frags"]);
                array_push($tmp, $row["deaths"]);
                array_push($tmp, $row["level"]);
                array_push($tmp, $row["clan_id"]);
                array_push($plend, $tmp);
            }
        }
    }
}

echo "<table style=\"float: left\">
<th align=\"center\"> № </th>
<th align=\"center\"> Никнейм </th>
<th align=\"center\"> Фраги за бой </th>
<th align=\"center\"> Смерти за бой</th>
<th align=\"center\"> Фраги </th>
<th align=\"center\"> Клан </th>
</tr>";
$cnt=1;
// print_r($plstart);
// print_r($plend);


foreach ($plend as $pl2) {
    foreach ($plstart as $pl1) {
        // echo " $pl1[0]==$pl2[0]";

        // foreach ($players2 as $pl2) {
        if ($pl1[1]==$pl2[1]) {
            $kil=$pl2[2] - $pl1[2];
            $dead=$pl2[3] - $pl1[3];
            if (($kil>0)||($dead>0)) {
                // echo " $pl1[0]==$pl2[0]";
                echo "<tr>";
                // echo "<td>{$pl1[1]}</td>";
                echo "<td align=\"center\">$cnt</td>";
                $cnt++;
                echo "<td>{$pl1[1]}</td>";
                echo "<td>$kil</td>";
                echo "<td>$dead</td>";
                echo "<td>{$pl2[2]}</td>";
                $name=GetClanName2($connection, $config, $pl1[5]);
                echo "<td>{$name}</td>";
                echo "</tr>";
            }
        }
    }
}
echo "</table>";
//
// $plall=array();
// $query = "\nSELECT * FROM {$config["base_database"]}.Players_fast WHERE timemark>=\"$end\" and timemark<=\"$resolved\" ORDER BY timemark ASC\n";
// echo $query;
// $result = $connection->query($query);
// // print_r($result);
// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         // print_r($row);
//         if ($clan_selected!=-1) {
//             if ($row["clan_id"]==$clan_selected) {
//                 // if (($row["clan_id"]==$attacker)||($row["clan_id"]==$defender)) {
//                 $tmp=array();
//                 array_push($tmp, $row["id"]);
//                 array_push($tmp, $row["nick"]);
//                 array_push($tmp, $row["frags"]);
//                 array_push($tmp, $row["deaths"]);
//                 array_push($tmp, $row["level"]);
//                 array_push($tmp, $row["clan_id"]);
//                 array_push($plall, $tmp);
//             }
//         } else {
//             if (($row["clan_id"]==$attacker)||($row["clan_id"]==$defender)) {
//                 $tmp=array();
//                 array_push($tmp, $row["id"]);
//                 array_push($tmp, $row["nick"]);
//                 array_push($tmp, $row["frags"]);
//                 array_push($tmp, $row["deaths"]);
//                 array_push($tmp, $row["level"]);
//                 array_push($tmp, $row["clan_id"]);
//                 array_push($plall, $tmp);
//             }
//         }
//     }
// }
// print_r($plall);
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
?>

	</body>
