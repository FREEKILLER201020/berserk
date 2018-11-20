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
		<link rel="stylesheet" href="css/style.css">
	</head>
	<header class="header sticky sticky--top js-header">

	<div class="grid">

		<nav class="navigation">
			<ul class="navigation__list navigation__list--inline">
				<li class="navigation__item"><a onclick="gotourl('index.php')">Статистика</a></li>
				<li class="navigation__item"><a onclick="gotourl('timetable.php')" >Расписание</a></li>
				<li class="navigation__item"><a onclick="gotourl('history.php')">История</a></li>
				<li class="navigation__item"><a onclick="gotourl('cities.php')" class="is-active">Города</a></li>
				<li class="navigation__item"><a onclick="gotourl('clans.php')">Кланы</a></li>
				<span id="dot" style="height: 10px;
			  width: 10px;
			  background-color: green;
			  border-radius: 50%;
			  display: inline-block;
				visibility: hidden;"></span>
			</ul>
		</nav>

	</div>

</header>
<script  src="js/index.js"></script>
<script>
<?php
$link=htmlentities($_SERVER['PHP_SELF']);
	$links=explode("/",$link);
	$res="";
	for ($i=0;$i<count($links)-1;$i++){
	$res=$res.$links[$i]."/";
}
?>
var active;
document.addEventListener('keydown', function(event) {
  if (event.code == 'KeyJ' && (event.ctrlKey || event.metaKey)) {
		active=true;
		document.getElementById("dot").style.visibility="visible";
		console.log(document.getElementById("dot"));
  }
});
function gotourl(url) {
	if (active==true){
		window.open("<?php echo $res?>"+url+"?results=true","_self");
	}
	else{
		window.open("<?php echo $res?>"+url,"_self");
	}
} </script>
	<?php
    setlocale(LC_ALL, 'Russian_Russia.65001', 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
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
            $query = "\nSELECT DISTINCT timemark FROM {$config["base_database"]}.Cities ORDER BY timemark DESC;\n";
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

                                if ($order=="name") {
                                    echo	"	<option selected value=\"name\"> Название </option>";
                                } else {
                                    echo	"	<option value=\"name\"> Название </option>";
                                }
                                // if ($order=="id") {
                                //     echo	"	<option selected value=\"id\"> ID </option>";
                                // } else {
                                //     echo	"	<option value=\"id\"> ID </option>";
                                // }
                                                                if ($order=="clan") {
                                                                    echo	"	<option selected value=\"clan\"> Клан </option>";
                                                                } else {
                                                                    echo	"	<option value=\"clan\"> Клан </option>";
                                                                }

                                                                echo " </select>";

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
<br><input type=\"submit\" name=\"formSubmit\" value=\" Обновить \" /><br>";

$where="";
if ($clan_selected>=0) {
    $where=$where."and clan=$clan_selected";
}
$today=date("Y-m-d");

$cities=array();
$query = "\nSELECT * FROM {$config["base_database"]}.Cities WHERE timemark=\"$time\" $where ORDER BY id;\n";
// echo $query;
$result = $connection->query($query);
// print_r($result);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // print_r($row);
        if ($row["clan"]!=null) {
            $tmp=array();
            array_push($tmp, $row["id"]);
            array_push($tmp, $row["name"]);
            array_push($tmp, $row["clan"]);
            array_push($cities, $tmp);
        }
    }
}

echo "<table style=\"float: left\">";
//<th align=\"center\"> № </th>
echo "<th align=\"center\"> № </th>
<th align=\"center\"> Название </th>
<th align=\"center\"> Клан </th>

</tr>";
$rowws=array();
foreach ($cities as $city) {
    $tmp=array();
    array_push($tmp, $city[0]);
    array_push($tmp, $city[1]);
    array_push($tmp, $city[2]);
    array_push($rowws, $tmp);
}

if ($order=="id") {
    $ord=0;
}
if ($order=="name") {
    $ord=1;
}
if ($order=="clan") {
    $ord=2;
}
if ($ord==1) {
    $tmp1=array();
    $tmp2=array();
    foreach ($rowws as $roww) {
        array_push($tmp1, $roww[1]);
    }
    // print_r($tmp1);
    natcasesort($tmp1);
    // print_r($tmp1);
    foreach ($tmp1 as $tmp11) {
        for ($a=0;$a<count($rowws);$a++) {
            // echo $tmp11;
            if ($rowws[$a][1]==$tmp11) {
                array_push($tmp2, $rowws[$a]);
            }
        }
    }
    $rowws=$tmp2;
    if ($order_way=="desc") {
        $rowws = array_reverse($rowws, false);
    }
    // array_sort_by(1, $rowws);
} else {
    if ($order_way=="desc") {
        for ($a=0;$a<count($rowws);$a++) {
            for ($b=0;$b<count($rowws);$b++) {
                if ($rowws[$a][$ord]>$rowws[$b][$ord]) {
                    $tmp=$rowws[$a];
                    $rowws[$a]=$rowws[$b];
                    $rowws[$b]=$tmp;
                }
            }
        }
    } else {
        for ($a=0;$a<count($rowws);$a++) {
            for ($b=0;$b<count($rowws);$b++) {
                if ($rowws[$a][$ord]<$rowws[$b][$ord]) {
                    $tmp=$rowws[$a];
                    $rowws[$a]=$rowws[$b];
                    $rowws[$b]=$tmp;
                }
            }
        }
    }
}
$cnt=1;
foreach ($rowws as $row) {
    // print_r($row);
    echo "<tr>";
    echo "<td>$cnt</td>";
    $cnt++;
    // echo "<td>$row[0]</td>";
    echo "<td>$row[1]</td>";
    $n1=GetClanName2($clans, $row[2]);
    echo "<td>$n1</td>";
    echo "</tr>";
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
function GetClanName2($clans, $id)
{
    foreach ($clans as $clan) {
        if ($clan[0]==$id) {
            return $clan[1];
        }
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
function array_sort_by($key, array &$array)
{
    return usort($array, function ($x, $y) use ($key) {
        return strnatcasecmp($x[$key] ?? null, $y[$key] ?? null);
    });
}
