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
        require("classes.php");
        require("data.php");
                $file  = file_get_contents(realpath(dirname(__FILE__))."/../config.json");
        $config = json_decode($file, true);
        $connection=Connect($config);
                $date=null;
                if (isset($_POST['formSubmit'])) {
                    $date= $_POST['date'];
                }
            $dates=array();
            $query = "\nSELECT DISTINCT timemark FROM {$config["base_database"]}.Players ORDER BY timemark DESC;\n";
            $result = $connection->query($query);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $tmp=array();
                    $split=explode("-", $row["timemark"]);
                    $split2=explode(" ", $split[2]);
                    $year=(int)$split[0];
                    $month=(int)$split[1];
                    $day=(int)$split2[0];
                    $ret="$split[1]-$split2[0]-$split[0]";
                    $ret2="$day-$month-$year";
                    array_push($tmp, $ret);
                    array_push($tmp, $ret2);
                    array_push($dates, $tmp);
                }
            }

    ?>
	<body>
		<script>
 		$(function() {
			function available(date) {
	 	var availableDates = [<?php 	echo "\"{$dates[0][1]}\""; for ($i=1;$i<count($dates);$i++) {
        echo ",\"{$dates[$i][1]}\"";
    }
         ?>];
	   dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
	   if ($.inArray(dmy, availableDates) != -1) {
	     return [true, "","Available"];
	   } else {
	     return [false,"","unAvailable"];
	   }
	 }
	 $('#date').datepicker({ beforeShowDay: available });

});
</script>
		<!-- Date: <input type="text" id="date_picker"> -->
		<form action="<?php echo htmlentities($_SERVER['PHP_SELF'])?>" method="post">
			Дата: <input type="text" name="date" id="date" size="12" value="<?php if ($date==null) {
             echo str_replace("-", "/", $dates[0][0]);
         } else {
             echo $date;
         }?>" />
				 <!-- <input type="submit" name="formSubmit" value="Select" /> -->

	</body>


<?php
// require("classes.php");
// require("data.php");
// $file  = file_get_contents("config.json");
// $config = json_decode($file, true);
// $connection=Connect($config);
// if (isset($_POST['formSubmit'])) {
// }
// echo "!!!!{$_POST['date']}???";
$time=GetLatestDate($connection, $config);
if ($date!=null) {
    $tmp=explode("/", $date);
    // print_r($tmp);
    $time="$tmp[2]-$tmp[0]-$tmp[1]";
}

$clan_selected=-1;
$order="frags";
$order_way="desc";
if (isset($_POST['formSubmit'])) {
    $clan_selected = $_POST['Clan'];
    $order = $_POST['Order'];
    $order_way = $_POST['Order_way'];

    // echo "\n %%%$order \n";
}


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

echo "<br>	Клан: <select name=\"Clan\">
						<option value=\"-1\"> Все кланы </option>
				";
foreach ($clans as $clan) {
    if ($clan[0]==$clan_selected) {
        echo "<option selected value=\"$clan[0]\">$clan[1]</option>";
    } else {
        echo "<option value=\"$clan[0]\">$clan[1]</option>";
    }
}

echo " </select>
			";

echo "<br>	Сортировать по: <select name=\"Order\">";

                                if ($order=="nick") {
                                    echo	"	<option selected value=\"nick\"> Никнейм </option>";
                                } else {
                                    echo	"	<option value=\"nick\"> Никнейм </option>";
                                }
                                if ($order=="frags") {
                                    echo	"	<option selected value=\"frags\"> Фраги </option>";
                                } else {
                                    echo	"	<option value=\"frags\"> Фраги </option>";
                                }
                                if ($order=="deaths") {
                                    echo	"<option selected value=\"deaths\"> Смерти </option>";
                                } else {
                                    echo	"<option value=\"deaths\"> Смерти </option>";
                                }
                                    if ($order=="level") {
                                        echo	"	<option selected value=\"level\"> Уровень </option>";
                                    } else {
                                        echo	"	<option value=\"level\"> Уровень </option>";
                                    }

            echo " </select>
";

                        echo "<select name=\"Order_way\">";
                                                        if ($order_way=="desc") {
                                                            echo	"	<option selected value=\"desc\"> По убыванию</option>";
                                                        } else {
                                                            echo	"	<option value=\"desc\"> По убыванию </option>";
                                                        }
                                                                                                                if ($order_way=="asc") {
                                                                                                                    echo	"<option selected value=\"asc\"> По возростанию </option>";
                                                                                                                } else {
                                                                                                                    echo	"<option value=\"asc\"> По возростанию </option>";
                                                                                                                }

                                    echo " </select>
												<br><input type=\"submit\" name=\"formSubmit\" value=\" Обновить \" /><br>

									</table>";

$where="";
if ($clan_selected>=0) {
    $where=$where."and clan_id=$clan_selected";
}
$today=date("Y-m-d");

$players=array();
if ($today==$time) {
    if ($clan_selected<0) {
        $all_clans=GetClans();
        // print_r($clans);
        foreach ($all_clans as $clan) {
            $pl=GetClanData($clan["id"]);
            // print_r($pl);
            foreach ($pl as $key=> $data) {
                // echo "$key => $data";
                if ($key=="players") {
                    foreach ($data as $player) {
                        // print_r($player);
                        $nick=Restring($player["nick"]);
                        $tmp=array();
                        array_push($tmp, $player["id"]);
                        array_push($tmp, $nick);
                        array_push($tmp, $player["frags"]);
                        array_push($tmp, $player["deaths"]);
                        array_push($tmp, $player["level"]);
                        array_push($tmp, $clan["id"]);
                        array_push($players, $tmp);
                        // $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";
                                        // echo $query;
                                        // $result = $connection->query($query);
                                        // if (!$result) {
                                        // 		die("Error during filling players table".$connection->connect_errno.$connection->connect_error);
                                        // }
                    }
                }
            }
        }
    } else {
        $pl=GetClanData($clan_selected);
        // print_r($pl);
        foreach ($pl as $key=> $data) {
            // echo "$key => $data";
            if ($key=="players") {
                foreach ($data as $player) {
                    // print_r($player);
                    $nick=Restring($player["nick"]);
                    $tmp=array();
                    array_push($tmp, $player["id"]);
                    array_push($tmp, $nick);
                    array_push($tmp, $player["frags"]);
                    array_push($tmp, $player["deaths"]);
                    array_push($tmp, $player["level"]);
                    array_push($tmp, $clan_selected);
                    array_push($players, $tmp);
                    // $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";
                                                                    // echo $query;
                                                                    // $result = $connection->query($query);
                                                                    // if (!$result) {
                                                                    // 		die("Error during filling players table".$connection->connect_errno.$connection->connect_error);
                                                                    // }
                }
            }
        }
    }
    if ($order=="nick") {
        $ord=1;
    }
    if ($order=="frags") {
        $ord=2;
    }
    if ($order=="deaths") {
        $ord=3;
    }
    if ($order=="level") {
        $ord=4;
    }
    if ($order_way=="desc") {
        for ($a=0;$a<count($players);$a++) {
            for ($b=0;$b<count($players);$b++) {
                if ($players[$a][$ord]>$players[$b][$ord]) {
                    $tmp=$players[$a];
                    $players[$a]=$players[$b];
                    $players[$b]=$tmp;
                }
            }
        }
    } else {
        for ($a=0;$a<count($players);$a++) {
            for ($b=0;$b<count($players);$b++) {
                if ($players[$a][$ord]<$players[$b][$ord]) {
                    $tmp=$players[$a];
                    $players[$a]=$players[$b];
                    $players[$b]=$tmp;
                }
            }
        }
    }
} else {
    $query = "\nSELECT * FROM {$config["base_database"]}.Players WHERE timemark=\"$time\" $where ORDER BY $order $order_way;\n";

    // echo $query;
    $result = $connection->query($query);
    // print_r($result);
    // echo "<table>
    // <th align=\"center\">NICK</th>
    // <th align=\"center\">FRAGS</th>
    // <th align=\"center\">DEATH</th>
    // <th align=\"center\">LEVEL</th>
    // <th align=\"center\">CLAN</th>
    // </tr>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tmp=array();
            array_push($tmp, $row["id"]);
            array_push($tmp, $row["nick"]);
            array_push($tmp, $row["frags"]);
            array_push($tmp, $row["deaths"]);
            array_push($tmp, $row["level"]);
            array_push($tmp, $row["clan_id"]);
            array_push($players, $tmp);
            // print_r($row);
        // echo "<tr>";
        // echo "<td>{$row["nick"]}</td>";
        // echo "<td>{$row["frags"]}</td>";
        // echo "<td>{$row["deaths"]}</td>";
        // echo "<td>{$row["level"]}</td>";
        // $name=GetClanName($connection, $config, $row["clan_id"]);
        // echo "<td>$name</td>";
        // echo "</tr>";
        }
    }
}
// print_r($players);
// echo "</table>";

$time2=date("Y-m-d", strtotime($time) - 60 * 60 * 24);
if (CheckDatee($connection, $config, $time2)==-1) {
    $time2=$time;
}
$query = "\nSELECT * FROM {$config["base_database"]}.Players WHERE timemark=\"$time2\" $where ORDER BY $order $order_way;\n";

// echo $query;
$result = $connection->query($query);

$players2=array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tmp=array();
        array_push($tmp, $row["id"]);
        array_push($tmp, $row["nick"]);
        array_push($tmp, $row["frags"]);
        array_push($tmp, $row["deaths"]);
        array_push($tmp, $row["level"]);
        array_push($tmp, $row["clan_id"]);
        array_push($players2, $tmp);
        // print_r($row);
        // echo "<tr>";
        // echo "<td>{$row["nick"]}</td>";
        // echo "<td>{$row["frags"]}</td>";
        // echo "<td>{$row["deaths"]}</td>";
        // echo "<td>{$row["level"]}</td>";
        // $name=GetClanName($connection, $config, $row["clan_id"]);
        // echo "<td>$name</td>";
        // echo "</tr>";
    }
}

$query = "\nSELECT * FROM {$config["base_database"]}.Eras ORDER BY started DESC limit 1;\n";
$result = $connection->query($query);
// echo $query;
$lbz=array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // print_r($row);
        $time3=$row["started"];
        $lbz1=explode(";", $row["lbz"]);
        foreach ($lbz1 as $lb) {
            $tmp=explode("=", $lb);
            array_push($lbz, $tmp);
        }
    }
}
// print_r($lbz);
// echo $time3;
$query = "\nSELECT * FROM {$config["base_database"]}.Players WHERE timemark=\"$time3\" $where ORDER BY $order $order_way;\n";

// echo $query;
$result = $connection->query($query);

$players3=array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tmp=array();
        array_push($tmp, $row["id"]);
        array_push($tmp, $row["nick"]);
        array_push($tmp, $row["frags"]);
        array_push($tmp, $row["deaths"]);
        array_push($tmp, $row["level"]);
        array_push($tmp, $row["clan_id"]);
        array_push($players3, $tmp);
        // print_r($row);
        // echo "<tr>";
        // echo "<td>{$row["nick"]}</td>";
        // echo "<td>{$row["frags"]}</td>";
        // echo "<td>{$row["deaths"]}</td>";
        // echo "<td>{$row["level"]}</td>";
        // $name=GetClanName($connection, $config, $row["clan_id"]);
        // echo "<td>$name</td>";
        // echo "</tr>";
    }
}



// echo "<table>
// <th align=\"center\"> Никнейм </th>
// <th align=\"center\"> Фраги </th>
// <th align=\"center\">Δ Фрагов </th>
// <th align=\"center\"> Смерти </th>
// <th align=\"center\">Δ Смертрей </th>
// <th align=\"center\"> Уровень </th>
// <th align=\"center\"> Клан </th>
// </tr>";
echo "<table style=\"float: left\">
<th align=\"center\"> № </th>
<th align=\"center\"> Никнейм </th>
<th align=\"center\"> Фраги </th>
<th align=\"center\"> Смерти </th>
<th align=\"center\"> Уровень </th>
<th align=\"center\"> Клан </th>
<th align=\"center\"> Фраги<br>в эре </th>
<th align=\"center\"> Смерти<br>в эре </th>
<th align=\"center\"> Содары </th>
<th align=\"center\"> Участия </th>
<th align=\"center\"> Очки </th>
<th align=\"center\"> ЛБЗ </th>

</tr>";
$cnt=1;
foreach ($players as $pl1) {
    foreach ($players3 as $pl2) {
        if ($pl1[0]==$pl2[0]) {
            // echo " $pl1[0]==$pl2[0]";
            echo "<tr>";
            // echo "<td>{$pl1[1]}</td>";
            echo "<td align=\"center\">$cnt</td>";
            $cnt++;
            echo "<td><p><a href=\"/test.php?nick={$pl1[1]}\">{$pl1[1]}</a></p></td>";
             echo "<td>{$pl1[2]}</td>";
            // // $a=$pl1[2]-$pl2[2];
            // // echo "<td>$a</td>";
             echo "<td>{$pl1[3]}</td>";
            // // $b=$pl1[3]-$pl2[3];
            // // echo "<td>$b</td>";
            echo "<td>{$pl1[4]}</td>";
            $name=GetClanName($connection, $config, $pl1[5], $time);
            echo "<td>$name</td>";
            $a=$pl1[2]-$pl2[2];
            echo "<td>$a</td>";
            $b=$pl1[3]-$pl2[3];
            echo "<td>$b</td>";
            $c=floor(2*$a+0.5*$b);
            echo "<td>$c</td>";
            $u=$a+$b;
            echo "<td>$u</td>";
            $o=5*$a+$b;
            echo "<td>$o</td>";
            $lbzz="";
            foreach ($lbz as $lb) {
                if ($lb[0]<=$u) {
                    $lbzz=$lb[1];
                }
            }
            echo "<td><div style=\"word-wrap: break-word;\">$lbzz</div></td>";
            echo "</tr>";
        }
    }
}
echo "</table>";

echo "<table style=\"float: left\">
<tr>
<th colspan=\"4\">Переходы игроков</th>
</tr>
<th align=\"center\"> № </th>
<th align=\"center\"> Никнейм </th>
<th align=\"center\"> Покинул клан </th>
<th align=\"center\"> Вступил клан </th>
</tr>";

$left=array();
foreach ($players as $pl1) {
    $was=0;
    foreach ($players2 as $pl2) {
        if ($pl1[0]==$pl2[0]) {
            $was=1;
            // echo " $pl1[0]==$pl2[0]";
            if ($pl1[5]!=$pl2[5]) {
                // echo " $pl1[0]==$pl2[0]";
                $name=GetClanName($connection, $config, $pl1[5], $time);
                $name2=GetClanName($connection, $config, $pl2[5], $time2);
                $tmp=array();
                array_push($tmp, $pl1[1]);
                array_push($tmp, $name2);
                array_push($tmp, $name);
                array_push($left, $tmp);
            }
        }
    }
    if ($was==0) {
        $name=GetClanName($connection, $config, $pl1[5], $time);
        $tmp=array();
        array_push($tmp, $pl1[1]);
        array_push($tmp, null);
        array_push($tmp, $name);
        array_push($left, $tmp);
    }
}

foreach ($players2 as $pl1) {
    $was=0;
    foreach ($players as $pl2) {
        if ($pl1[0]==$pl2[0]) {
            $was=1;
            // echo " $pl1[0]==$pl2[0]";
        }
    }
    if ($was==0) {
        $name=GetClanName($connection, $config, $pl1[5], $time2);
        $tmp=array();
        array_push($tmp, $pl1[1]);
        array_push($tmp, $name);
        array_push($tmp, null);
        array_push($left, $tmp);
    }
}

// print_r($left);
if (count($left)==0) {
    echo "<tr>";
    echo "<td colspan=\"4\">Нет Данных</td>";
    echo "</tr>";
} else {
    $cnt=1;
    foreach ($left as $pl) {
        echo "<tr>";
        // echo "<td>{$pl[0]}</td>";
        echo "<td align=\"center\">$cnt</td>";
        $cnt++;
        echo "<td><p><a href=\"/test.php?nick={$pl[0]}\">{$pl[0]}</a></p></td>";
        echo "<td>{$pl[1]}</td>";
        echo "<td>{$pl[2]}</td>";
        echo "</tr>";
    }
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
