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


$file  = file_get_contents(realpath(dirname(__FILE__))."/../config.json");
$config = json_decode($file, true);
$connection=Connect($config);
$nick=$_GET['nick'];
// $name="the_pooh";
$query = "\nSELECT * FROM {$config["base_database"]}.Players WHERE nick=\"$nick\" ORDER BY timemark asc;\n";
// echo $query;
$result = $connection->query($query);
// print_r($result);
$dataPoints=array();
$info=array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tmp=array();
        array_push($tmp, $row["clan_id"]);
        array_push($tmp, $row["timemark"]);
        array_push($info, $tmp);

        $name=GetClanName($connection, $config, $row["clan_id"], $row["timemark"]);
        // echo $name;
        array_push($dataPoints, array("y" => $row["clan_id"],"label" => strtotime($row["timemark"]),"date"=>$name));
    }
}
// print_r($info);
$i=0;
// while ($i<count($info)) {
//     $j=$i;
//     // echo "{$info[$i][0]} and {$info[$j][0]}";
//     while ($info[$i][0]==$info[$j][0]) {
//         // echo "{$info[$i][0]} and {$info[$j][0]}";
//         // echo " $j is ";
//         $j++;
//         if ($j==count($info)) {
//             goto t1;
//         }
//     }
//     t1:
//     $j--;
//
//     // echo " {$info[$i][1]}->{$info[$j][1]}";
//     $name=GetClanName($connection, $config, $info[$i][0], $info[$i][1]);
//     array_push($dataPoints, array("label" => $name, "y" => array(strtotime($info[$i][1])*1000,strtotime($info[$j][1])*1000),"date"=>array("\"{$info[$i][1]}\"","\"{$info[$j][1]}\"")));
//
//     // echo " $i->$j";
//     $i=$j+1;
// }
// print_r($dataPoints);
// $dataPoints = array(
//     array("label"=> "Piano", "y"=> array(28, 4186))
//     // array("label"=> "Trumpet", "y"=> array(165, 988)),
//     // array("label"=> "Violin", "y"=> array(196, 3136)),
//     // array("label"=> "Acoustic Guitar", "y"=> array(82, 1397)),
//     // array("label"=> "Concert Flute", "y"=> array(262, 1976)),
//     // array("label"=> "4 String Bass Guitar", "y"=> array(41, 262)),
//     // array("label"=> "Electric Guitar", "y"=> array(82, 1397))
// );

// $dataPoints = array(
//     array("y" => 25, "label" => "Sunday"),
//     array("y" => 15, "label" => "Monday"),
//     array("y" => 25, "label" => "Tuesday"),
//     array("y" => 5, "label" => "Wednesday"),
//     array("y" => 10, "label" => "Thursday"),
//     array("y" => 0, "label" => "Friday"),
//     array("y" => 20, "label" => "Saturday")
// );
print_r($dataPoints);


?>
<!DOCTYPE HTML>
<html>
<head>
<script>
// window.onload = function () {
//
// var chart = new CanvasJS.Chart("chartContainer", {
// 	title: {
// 		text: "Состояние игрока <?php echo $nick ?> в кланах"
// 	},
// 	axisY:{
// 		title: "Date",
// 		suffix: "",
// 		logarithmic: false,
// 		includeZero: false,
//     labelFormatter: function (e) {
//       return CanvasJS.formatDate( e.value, "YYYY-MM-DD");
//     }
// 	},
// 	toolTip: {
// 		shared: false,
// 		reversed: true
// 	},
// 	theme: "light1",
// 	data: [
// 		{
// 			type: "rangeBar",
// 			indexLabel: "{date[#index]}",
//       yValueType: "dateTime",
//       toolTipContent: "<b>{label}</b>: from {date[0]} to {date[1]}",
//       // xValueFormatString: "hh:mm:ss TT",
// 			dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
// 		}
// 	]
// });
//
// chart.render();
//
// }

window.onload = function () {

var chart = new CanvasJS.Chart("chartContainer", {
  	title: {
  		text: "Состояние игрока <?php echo $nick ?> в кланах"
  	},
  axisX:{
  	// 	title: "Date",
  	// 	suffix: "",
  	// 	logarithmic: false,
  	// 	includeZero: false,
    labelFormatter: function (e) {
      console.log(e);
      console.log(new Date(e.label*1000));
        return CanvasJS.formatDate(new Date(e.label*1000), "YYYY-MM-DD");
      }

  	},
	axisY: {
    logarithmic: false,
    includeZero: false,
    // labelFormatter: function (e) {
    //   // console.log(e);
    //     return "test";
    //     // CanvasJS.formatDate( e.lable, "YYYY-MM-DD");
    //   }
	},
	data: [{
		type: "line",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();

}
</script>
</head>
<body>
<div id="chartContainer" style="height: 500; width: 99%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>
