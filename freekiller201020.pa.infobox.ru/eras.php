<html>
	<head>
		<style>
			table, th, td {
    		border: 1px solid black;
				margin-right: 2%;
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
			* {
				font-family:'Arial';
				font-size: normal;
			}
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
	$num=null;
	$start=null;
	$end=null;
	$lbz=null;
	// print_r($_POST);
	if (isset($_POST['formDelete'])) {
		$query = "DELETE FROM {$config["base_database"]}.Eras WHERE id={$_POST['num']};";
		// echo $query;
		$result = $connection->query($query);
		if (!$result) {
				die("Error during adding era".$connection->connect_errno.$connection->connect_error);
		}
	}
	if (isset($_POST['formSubmit'])) {
		// print_r($_POST);
		$num=$_POST['num'];
		$d=explode("/",$_POST['start']);
		$start=$d[2]."-".$d[0]."-".$d[1];
		$d=explode("/",$_POST['end']);
		$end=$d[2]."-".$d[0]."-".$d[1];
		$lbz5=$_POST['lbz5'];
		$lbz10=$_POST['lbz10'];
		$lbz15=$_POST['lbz15'];
		$lbz25=$_POST['lbz25'];
		$lbz="5=$lbz5;10=$lbz10;15=$lbz15;25=$lbz25";

		if (($num!=null) && ($start!=null) && ($end!=null) && ($lbz!=null)){
			$query = "INSERT INTO {$config["base_database"]}.Eras (id,started,ended,lbz) VALUES ($num,'$start','$end','$lbz');\n";
			// echo $query;
			$result = $connection->query($query);
			if (!$result) {
					die("Error during adding era".$connection->connect_errno.$connection->connect_error);
			}
		}
	}
	// if (isset($_POST['formEdit'])) {
	// 	// print_r($_POST);
	// 	$edit=1;
	// 	$num=$_POST['num'];
	// 	$d=explode("/",$_POST['start']);
	// 	$start=$d[2]."-".$d[0]."-".$d[1];
	// 	$d=explode("/",$_POST['end']);
	// 	$end=$d[2]."-".$d[0]."-".$d[1];
	// 	$lbz5=$_POST['lbz5'];
	// 	$lbz10=$_POST['lbz10'];
	// 	$lbz15=$_POST['lbz15'];
	// 	$lbz25=$_POST['lbz25'];
	// 	$lbz="5=$lbz5;10=$lbz10;15=$lbz15;25=$lbz25";
	// }
	?>
  <script>
    $( function() {
      $( "#date" ).datepicker();
    } );
    </script>
    <script>
      $( function() {
        $( "#date2" ).datepicker();
      } );
      </script>
    <!-- Date: <input type="text" id="date_picker"> -->
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF'])?>" method="post">
      <?php
        $last=0;
        $query = "\nSELECT * FROM {$config["base_database"]}.Eras ORDER BY id DESC limit 1;\n";
        // echo $query;
        $result = $connection->query($query);
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $last=$row["id"];
          }
        }
        $last++;
       ?>
      №: <input type="text" name="num" size="12" value="<?php echo $last ?>" />
      Начало: <input type="text" name="start" id="date" size="12" value="<?php echo date("m/d/Y")?>" />
      Конец: <input type="text" name="end" id="date2" size="12" value="<?php $a=date("Y-m-d"); echo date("m/d/Y",strtotime("$a 00:00:00") + 30 * 60 * 60 * 24)?>" />
      <br>ЛБЗ-5: <input type="text" name="lbz5" size="12" value="Рарка" />
      ЛБЗ-10: <input type="text" name="lbz10" size="12" value="1б" />
      ЛБЗ-15: <input type="text" name="lbz15" size="12" value="1б + 2б" />
      ЛБЗ-25: <input type="text" name="lbz25" size="12" value="Ультра" />
      <input type=submit name=formSubmit value= Добавить ><br>
    </form>

<?php

$eras=array();
$query = "\nSELECT * FROM {$config["base_database"]}.Eras ORDER BY id DESC;\n";
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
// print_r($eras);
?>

<table >
<tr>
<th align=center> № </th>
<th align=center> Начало </th>
<th align=center> Конец </th>
<th align=center> ЛБЗ </th>
<th align=center>  </th>
</tr>
<?php
  foreach ($eras as $era) {
    $lbz=array();
    echo "<tr>";
    echo "<td align=center>$era[0]</td>";
    echo "<td align=center>$era[1]</td>";
    echo "<td align=center>$era[2]</td>";
    // echo "<td align=center>$era[3]</td>";
    $lbz1=explode(";", $era[3]);
		foreach ($lbz1 as $lb) {
				$tmp=explode("=", $lb);
				array_push($lbz, $tmp);
		}
    echo "<td><table >
    <tr>";
    foreach ($lbz as $lb) {
      echo "<tr>";
      echo "<td align=center>$lb[0]</td>";
      echo "<td align=center>$lb[1]</td>";
      echo "</tr>";
    }
    echo "</tr>";
    echo "</table></td>";
    echo " <td align=center>
    <form id=$era[0] $action=". htmlentities($_SERVER['PHP_SELF'])." method=\"post\">
    <input type=\"hidden\" name=\"num\" value=\"".$era[0]."\" />
		<input type=\"hidden\" name=\"start\" value=\"".$era[1]."\" />
		<input type=\"hidden\" name=\"end\" value=\"".$era[2]."\" />
		<input type=\"hidden\" name=\"lbz5\" value=\"".$lbz[0][1]."\" />
		<input type=\"hidden\" name=\"lbz10\" value=\"".$lbz[1][1]."\" />
		<input type=\"hidden\" name=\"lbz15\" value=\"".$lbz[2][1]."\" />
		<input type=\"hidden\" name=\"lbz25\" value=\"".$lbz[3][1]."\" />
    <input type=submit name=formDelete value= Удалить  onclick=\"return confirm('Действительно удалить?');\" >
		<!-- <input type=submit name=formEdit value= Изменить > -->
		</form>
		</td>";
    echo "</tr>";
  }
 ?>
</table>
</html>

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
 ?>
