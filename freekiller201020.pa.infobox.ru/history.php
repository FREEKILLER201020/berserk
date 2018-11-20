<html>
	<head>
		<style>
		input[type=submit] {
  background-color: #6c7ae0;
  border: 0;
  border-radius: 5px;
  cursor: pointer;
  color: #fff;
  font-size:18px;
  font-weight: normal;
  line-height: 1;
  padding: 5px;
  width: 150;
	margin-left: 5;
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


		</style>
		<script src="jquery.js"></script>
		<script src="jquery-ui.js"></script>
		<link href="jquery-ui.css" rel="stylesheet">
		<!-- <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet"> -->
		<link rel="stylesheet" href="css/style.css">
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
		<!--===============================================================================================-->
			<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
		<!--===============================================================================================-->
			<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
		<!--===============================================================================================-->
			<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
		<!--===============================================================================================-->
			<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
		<!--===============================================================================================-->
			<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
		<!--===============================================================================================-->
			<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
		<!--===============================================================================================-->
			<link rel="stylesheet" type="text/css" href="css/util.css">
			<link rel="stylesheet" type="text/css" href="css/main.css">
		<!--===============================================================================================-->
	</head>

	<body>
		<header class="header sticky sticky--top js-header">

		<div class="grid">

			<nav class="navigation">
				<ul class="navigation__list navigation__list--inline">
					<li class="navigation__item"><a onclick="gotourl('index.php')" >Статистика</a></li>
					<li class="navigation__item"><a onclick="gotourl('timetable.php')" >Расписание</a></li>
					<li class="navigation__item"><a onclick="gotourl('history.php')" class="is-active">История</a></li>
					<li class="navigation__item"><a onclick="gotourl('cities.php')">Города</a></li>
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
require("classes.php");
require("data.php");
session_start();
// if (($_SESSION['u']==null)||($_SESSION['p']==null)) {
//     echo "alert (\"error!\")";
//     exit();
// }
        $file  = file_get_contents(realpath(dirname(__FILE__))."/../config.json");
$config = json_decode($file, true);
$file  = file_get_contents(realpath(dirname(__FILE__))."/../clans.json");
$img = json_decode($file, true);
$connection=Connect($config);

$era_selected=-1;
if (isset($_POST['era'])) {
	$era_selected= $_POST['era'];
}

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

$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
$url .= $_SERVER['SERVER_NAME'];
$url .= ":".$_SERVER['SERVER_PORT'];
$url .= $_SERVER['REQUEST_URI'];

$url =dirname($url)."/fast_data2.php";

$eras=array();
$query = "\nSELECT * FROM {$config["base_database"]}.Eras ORDER BY started DESC;\n";
$result = $connection->query($query);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		if ($era_selected==$row["id"]){
			$start=$row["started"];
			$end=$row["ended"];
		}
		$tmp=array();
		array_push($tmp, $row["id"]);
		array_push($tmp, $row["started"]);
		array_push($tmp, $row["ended"]);
		array_push($tmp, $row["lbz"]);
		array_push($tmp, $row["points"]);
		array_push($eras, $tmp);
	}
}


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
// print_r($_POST);
$clan_selected=-1;
if (isset($_POST['formSubmit'])) {
    $clan_selected = $_POST['Clan'];
}


$attacks=array();
if ($era_selected==-1){
	$query = "\nSELECT * FROM {$config["base_database"]}.Attacks_fast ORDER BY resolved asc\n";
}
else {
	foreach($eras as $era){
		if ($era[0]==$era_selected){
			$start=$era[1];
			$ended=$era[2];
		}
	}
	$query = "\nSELECT * FROM {$config["base_database"]}.Attacks_fast WHERE resolved>=\"$start\" and resolved<=\"$ended\" ORDER BY resolved asc\n";
	// echo $query;
}
// echo $query;
$offset=$_POST['offset'];
$zone=$_POST['zone'];
if (isset($offset)){
	$result = $connection->query($query);
}
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

?>
<div class="limiter">
	<div class="container-table100">
		<div class="wrap-table100">
			<form style="display:inline;" action="<?php echo htmlentities($_SERVER['PHP_SELF'])?>" method="post" id=id="setoffset">
				<input id="offset" type="hidden" name="offset" value="<?php echo $offset ?>" />
				<p style="display:inline; margin-left: 5; font-size: 18px"> Эра: </p>
				<select style="-webkit-appearance: menulist-button; height:1.4em;" name="era">
					<option value="-1"> --- </option>
					<?php
					foreach ($eras as $era) {
							if ($era[0]==$era_selected) {
									echo "<option selected value=\"$era[0]\">$era[0] [$era[1] - $era[2]]</option>";
							} else {
									echo "<option value=\"$era[0]\">$era[0] [$era[1] - $era[2]]</option>";
							}
					}
					 ?>
				</select>
			<?php
            if (isset($_POST['formSubmit'])) {
                echo "<input type=\"hidden\" method=\"post\" value=\"{$_POST['results']}\" name=\"results\" />";
            }
            if (isset($_GET['results'])) {
                echo "<input type=\"hidden\" method=\"post\" value=\"{$_GET['results']}\" name=\"results\" />";
            }
            echo "<p style=\"display:inline; margin-left: 5; font-size: 18px\">Клан: <select style=\"   -webkit-appearance: menulist-button;
height:1.4em;
        \" name=\"Clan\">
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

            echo " </select></p>";
						?>
						<p style="display:inline; margin-left: 5; font-size: 18px"> TimeZone: </p>
						<input style="display:inline; margin-left: 5; font-size: 18px" id="zone" type="text" name="zone" value="<?php echo $zone; ?>" />
            <?php
						echo "<input style=\"display:inline; font-size: 18px\" type=\"submit\" name=\"formSubmit\" value=\" Обновить \" /></form> <br><br>";?>


			<div class="table100 ver1 m-b-110">
				<div class="table100-head">
					<table>
						<thead>
							<tr class="row100 head">
								<th class="cell100 column1"> № </th>
								<!-- <th class="cell100 column2"> From </th> -->
								<!-- <th class="cell100 column3"> To </th> -->
								<th class="cell100 column2"> </th>
								<th class="cell100 column3"> Атакует </th>
								<th class="cell100 column4"> Город</th>
								<th class="cell100 column5"> </th>
								<th class="cell100 column6"> Защищается </th>
								<th class="cell100 column7"> Город</th>
								<!-- <th class="cell100 column6"> Declared </th> -->
								<th class="cell100 column8"> Начало боя </th>
								<th class="cell100 column9"> Конец боя </th>
								<th class="cell100 column10"> Победитель </th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="table100-body js-pscroll">
					<table>
						<tbody>
<?php

if (!isset($offset)){
  $a=htmlentities($_SERVER['PHP_SELF']);
  echo "
    <script>
    var offset = new Date().getTimezoneOffset();
    console.log(offset);
    document.getElementById(\"offset\").value=offset;
    console.log(document.getElementById(\"offset\").value);
		document.getElementById(\"zone\").value=Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.getElementById(\"setoffset\").submit();
    </script>
  ";
}
// echo "<table style=\"float: left\">
// <th align=\"center\"> № </th>
// <th align=\"center\"> From </th>
// <th align=\"center\"> To </th>
// <th align=\"center\"> Attacker </th>
// <th align=\"center\"> Defender </th>
// <th align=\"center\"> Declared </th>
// <th align=\"center\"> Resolved </th>
// <th align=\"center\"> Ended </th>
// </tr>";
$cnt=1;
if (count($attacks)==0){
	echo "<td colspan=\"10\"  class=\"cell100 column2\">Нет данных</td>";
}
foreach ($attacks as $attack) {
    if ($attack[6]!=null) {
        if (strtotime($attack[6])>=strtotime($attack[5])) {
            // foreach ($players2 as $pl2) {
            //     if ($pl1[0]==$pl2[0]) {
            // echo " $pl1[0]==$pl2[0]";
            echo "<tr class=\"row100 body\">";
            // echo "<td>{$pl1[1]}</td>";
            echo "<td text-align=\"center\" class=\"cell10 column1\">$cnt</td>";
            $cnt++;
            // echo "<td class=\"cell100 column2\">{$attack[0]}</td>";
            // echo "<td class=\"cell100 column3\">{$attack[1]}</td>";
            $name1=GetClanName2($connection, $config, $attack[2]);
            $name2=GetClanName2($connection, $config, $attack[3]);
            echo "<td class=\"cell100 column2\"><img src=\"clans/{$img[$attack[2]]}.jpg\"></td>";
            echo "<td class=\"cell100 column3\">{$name1}</td>";
            echo "<td class=\"cell100 column4\">{$attack[0]}</td>";
            echo "<td class=\"cell100 column5\"><img src=\"clans/{$img[$attack[3]]}.jpg\"></td>";
            echo "<td class=\"cell100 column6\">{$name2}</td>";
            echo "<td class=\"cell100 column7\">{$attack[1]}</td>";
            $dt1=CorrectDate($attack[4], $offset*60*(-1));
            $dt2=CorrectDate($attack[5], $offset*60*(-1));
            // echo "<td class=\"cell100 column6\">{$dt1}</td>";
            echo "<td class=\"cell100 column8\">{$dt2}</td>";
            if ($attack[6]==null) {
                if ($attack[4]>=date("Y-m-d H:i:s")) {
                    echo "<td class=\"cell100 column9\"> В процессе </td>";
                } else {
                    echo "<td class=\"cell100 column9\"> Предстоящий бой </td>";
                }
            } else {
                // echo "<td>{$attack[6]}</td>";
                $dt3=CorrectDate($attack[6], $offset*60*(-1));
                if (($_GET['results']=="true") || ($_POST['results']=="true")) {
                    echo "<td class=\"cell100 column9\"><p><a href=\"$url?from={$attack[0]}&to={$attack[1]}&attacker={$attack[2]}&defender={$attack[3]}&declared={$attack[4]}&resolved={$attack[5]}&end={$attack[6]}&clan_selected=$clan_selected\">{$dt3}</a></p></td>";
                } else {
                    echo "<td class=\"cell100 column9\">{$dt3}</td>";
                }
            }
            $cities1_e=GetCitiesList($connection, $config, $attack[3], $attack[6]);
            $was=0;
            // print_r($cities1_e);
            foreach ($cities1_e as $city) {
                if ($city[1]==$attack[1]) {
                    $was=1;
                }
            }
            if ($was==1) {
                echo "<td class=\"cell100 column10\"> $name2 </td>";
            } else {
                echo "<td class=\"cell100 column10\"> $name1 </td>";
            }
            echo "</tr>";
            //     }
        }
    }
}
?>
</tbody>
</table>
</div>
</div>
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

function GetCitiesList($connection, $config, $id, $time)
{
    $cities=array();
    $query = "\nSELECT * FROM {$config["base_database"]}.Cities_fast WHERE timemark=(Select timemark FROM {$config["base_database"]}.Cities_fast WHERE timemark>=\"$time\" ORDER BY timemark ASC limit 1) and clan=$id ORDER BY id;\n";
    // echo $query;
    // exit();
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
    return $cities;
}
?>

	</body>
