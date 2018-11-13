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
	<?php
    setlocale(LC_ALL, 'Russian_Russia.65001', 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
        require("classes.php");
        require("data.php");
                $file  = file_get_contents(realpath(dirname(__FILE__))."/../config.json");
        $config = json_decode($file, true);
        $connection=Connect($config);
        ?>

<body>
<?php
$clans=array();
$query = "\nSELECT * FROM {$config["base_database"]}.Clans WHERE timemark=\"$time\" ORDER BY id;\n";
// echo $query;
$result = $connection->query($query);
// print_r($result);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // print_r($row);
        $tmp=array();
        array_push($tmp, $row["id"]);
        array_push($tmp, $row["title"]);
        array_push($clans, $tmp);
    }
}
$query = "\nSELECT nick FROM {$config["base_database"]}.Players WHERE id={$_GET['id']}  ORDER BY timemark asc limit 1;\n";
// echo $query;
$result = $connection->query($query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Информация по игроку {$row['nick']} <br><br>";
    }
}
echo "<table style=\"float: left\">";
echo "<tr><th align=\"center\"> Дата </th>
<th align=\"center\"> Событие </th>
</tr>";
$query = "\nSELECT * FROM {$config["base_database"]}.Players WHERE id={$_GET['id']}  ORDER BY timemark asc;\n";
// echo $query;
$prow=array();
$result = $connection->query($query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (count($prow)>0) {
            if ($prow[1]!=$row["nick"]) {
                echo "<tr>";
                echo "<td>{$row["timemark"]}</td>";
                echo "<td>Player has changed his nick from {$prow[1]} to {$row["nick"]}</td>";
                echo "</tr>";
            }
            if ($prow[5]!=$row["clan_id"]) {
                echo "<tr>";
                echo "<td>{$row["timemark"]}</td>";
                if (($prow[5]!=null)&&($row["clan_id"]!=null)) {
                    // echo "<tr>";
                    // echo "<td>{$row["timemark"]}</td>";
                    $name=GetClanName2($connection, $config, $clans, $prow[5]);
                    $name2=GetClanName2($connection, $config, $clans, $row["clan_id"]);
                    echo "<td>Player has changed his clan from {$name} to {$name2}</td>";
                    // echo "</tr>";
                }
                if (($prow[5]!=null)&&($row["clan_id"]==null)) {
                    // echo "<tr>";
                    // echo "<td>{$row["timemark"]}</td>";
                    $name=GetClanName2($connection, $config, $clans, $prow[5]);
                    // $name2=GetClanName2($connection, $config, $clans, $row["clan_id"]);
                    echo "<td>Player has left clan {$name}</td>";
                    // echo "</tr>";
                }
                if (($prow[5]==null)&&($row["clan_id"]!=null)) {
                    // echo "<td>{$row["timemark"]}</td>";
                    // $name=GetClanName2($connection, $config, $clans, $prow[5]);
                    $name2=GetClanName2($connection, $config, $clans, $row["clan_id"]);
                    echo "<td>Player has joind clan {$name2}</td>";
                }
                echo "</tr>";
            }
        }
        $tmp=array();
        $tmp=array();
        array_push($tmp, $row["id"]);
        array_push($tmp, $row["nick"]);
        array_push($tmp, $row["frags"]);
        array_push($tmp, $row["deaths"]);
        array_push($tmp, $row["level"]);
        array_push($tmp, $row["clan_id"]);
        $prow=$tmp;
    }
}
echo "</table>";
 ?>
</body>


<?php
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
function GetClanName3($connection, $config, $id)
{
    $query = "SELECT * FROM {$config["base_database"]}.Clans WHERE id=$id ORDER BY timemark DESC;\n";
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
function GetClanName2($connection, $config, $clans, $id)
{
    foreach ($clans as $clan) {
        if ($clan[0]==$id) {
            return $clan[1];
        }
    }
    return GetClanName3($connection, $config, $id);
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


function Restring($string)
{
    return str_replace("'", "''", $string); // Replaces all spaces with hyphens.
}


function ReDate1($string)
{
    return str_replace("T", " ", ReDate2($string)); // Replaces all spaces with hyphens.
}
function ReDate2($string)
{
    return str_replace("Z", "", $string); // Replaces all spaces with hyphens.
}
function array_sort_by($key, array &$array)
{
    return usort($array, function ($x, $y) use ($key) {
        return strnatcasecmp($x[$key] ?? null, $y[$key] ?? null);
    });
}
