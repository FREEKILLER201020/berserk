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
				<li class="navigation__item"><a onclick="gotourl('cities.php')">Города</a></li>
				<li class="navigation__item"><a onclick="gotourl('clans.php')" class="is-active">Кланы</a></li>
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
				require("web_classes.php");
        require("data.php");
                $file  = file_get_contents(realpath(dirname(__FILE__))."/../config.json");
        $config = json_decode($file, true);
        $connection=Connect($config);
				$time=GetLatestDate($connection, $config);
				if ($date!=null) {
				    // if ($date[2]=="/"){
				    // 	$d=explode("/",$date);
				    // 	$time=$d[2]."-".$d[1]."-".$d[0];
				    // }
				    // else {
				    $time=$date;
				    // }
				}
        $clans=array();
				$tmp=GetClans();
				foreach ($tmp as $clan) {
					array_push($clans,new Web_Clan($clan["id"],$clan["title"],$clan["created"]));
				}
				foreach ($clans as $clan) {
					$clan->FindImg();
					$clan->FindLink();
					$clan->FindSLink();
				}
				?>
				<table style=float: left>
				<th align=center>  </th>
				<th align=center> Название </th>
				<th align=center> Дата регистрации </th>
				<th align=center> Ссылка на клан </th>
				<?php
				if (isset($_GET['results'])){
					echo "<th align=center> Ссылка на разведку </th>";
				} ?>

				</tr>
				<?php
				foreach ($clans as $clan) {
						// print_r($row);
						echo "<tr>";
						echo "<td><img src=\"$clan->img\"></td>";
						echo "<td>$clan->name</td>";
						echo "<td>$clan->created</td>";
						if (isset($clan->link)){
							echo "<td><a href=\"{$clan->link}\">Ссылка</a></td>";
						}
						else{
							echo "<td></td>";
						}
						if (isset($_GET['results'])){
							if (isset($clan->spy_link)){
								echo "<td><a href=\"{$clan->spy_link}\">Ссылка</a></td>";
							}
							else{
								echo "<td></td>";
							}
						}
						// echo "<td>$clan->spy_link</td>";
						echo "</tr>";
				}
				 ?>
				</table>
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
