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
        $date=date("Y-m-d");
        if (isset($_POST['formSubmit'])) {
            $date= $_POST['date'];
        }
        if ($date[2]=="/") {
            $d=explode("/", $date);
            $date=$d[2]."-".$d[0]."-".$d[1];
        }
        $nickname=null;
        if (isset($_POST['nickname'])) {
            $nickname= $_POST['nickname'];
        }
        $era_selected=-1;
        if (isset($_POST['era'])) {
            $era_selected= $_POST['era'];
        }
        // $is=-1;
        // if ($era_selected!=-1){
        // 	$query = "\nSELECT * FROM {$config["base_database"]}.Eras where started<=\"$dt\" and ended>=\"$dt\" ORDER BY started DESC;\n";
        // 	// echo $query;
        // 	$result = $connection->query($query);
        // 	if ($result->num_rows > 0) {
        // 		while ($row = $result->fetch_assoc()) {
        // 			$id=$row["id"];
        // 		}
        // 	}
        // 	$is=1;
        // }
        // if ($era_selected!=-1) {
        //     $query = "\nSELECT * FROM {$config["base_database"]}.Eras where id=$era_selected ORDER BY started DESC;\n";
        //     // echo $query;
        //     $result = $connection->query($query);
        //     if ($result->num_rows > 0) {
        //         while ($row = $result->fetch_assoc()) {
        //             $date=$row["ended"];
        //         }
        //     }
        // }
        if ($date!=null) {
            $dt=$date;
        } else {
            $dt=date("Y-m-d");
        }
            // $active_era=-1;
            // $query = "\nSELECT * FROM {$config["base_database"]}.Eras where started<=\"$dt\" and ended>=\"$dt\" ORDER BY started DESC;\n";
            // // echo $query;
            // $result = $connection->query($query);
            // if ($result->num_rows > 0) {
            // 	while ($row = $result->fetch_assoc()) {
            // 		$active_era=$row["id"];
            // 	}
            // }
        // }
        // if ($active_era!=-1) {
        //     $era_selected=$active_era;
        // }
        $eras=array();
        $query = "\nSELECT * FROM {$config["base_database"]}.Eras ORDER BY started DESC;\n";
        $result = $connection->query($query);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($era_selected==$row["id"]) {
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
        $dates=array();
        if ($era_selected!=-1) {
            $query = "\nSELECT DISTINCT timemark FROM {$config["base_database"]}.Players WHERE timemark>=\"$start\" and timemark<=\"$end\" ORDER BY timemark DESC;\n";
        } else {
            $query = "\nSELECT DISTINCT timemark FROM {$config["base_database"]}.Players ORDER BY timemark DESC;\n";
        }
                // echo $query;
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
	<script>
 		$(function() {
			function available(date) {
		 		var availableDates = [<?php 	echo "\"{$dates[0][1]}\""; for ($i=1;$i<count($dates);$i++) {
            echo ",\"{$dates[$i][1]}\"";
        }?>];
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
			Дата: <input type="text" name="date" id="date" size="12" value="<?php if ($date[4]=="-") {
            $d=explode("-", $date);
            echo $d[1]."/".$d[2]."/".$d[0];
        } else {
            echo $date;
        }?>" />
				 Эра:
				 <select name="era">
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
				 	<br>Никнейм: <input type="text" id="nickname" name="nickname" size="12" value="<?php if ($nickname!=null) {
                          echo $nickname;
                      }?>" >
<?php
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
$clan_selected=-1;
$order="frags";
$order_way="desc";
if (isset($_POST['formSubmit'])) {
    $clan_selected = $_POST['Clan'];
    $order = $_POST['Order'];
    $order_way = $_POST['Order_way'];
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
if ($order=="clan") {
    echo	"	<option selected value=\"clan\"> Клан </option>";
} else {
    echo	"	<option value=\"clan\"> Клан </option>";
}
if ($era_selected!=-1) {
    if ($order=="fragse") {
        echo	"	<option selected value=\"fragse\"> Фраги в эре </option>";
    } else {
        echo	"	<option value=\"fragse\"> Фраги в эре </option>";
    }
    if ($order=="deathse") {
        echo	"	<option selected value=\"deathse\"> Смерти в эре </option>";
    } else {
        echo	"	<option value=\"deathse\"> Смерти в эре </option>";
    }
    if ($order=="sodars") {
        echo	"	<option selected value=\"sodars\"> Содары </option>";
    } else {
        echo	"	<option value=\"sodars\"> Содары </option>";
    }
    if ($order=="actions") {
        echo	"	<option selected value=\"actions\"> Участия </option>";
    } else {
        echo	"	<option value=\"actions\"> Участия </option>";
    }
    if ($order=="points") {
        echo	"	<option selected value=\"points\"> Очки </option>";
    } else {
        echo	"	<option value=\"points\"> Очки </option>";
    }
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
<br><input type=\"submit\" name=\"formSubmit\" value=\" Обновить \" /><br>
</table>";

$where="";
if ($clan_selected>=0) {
    $where=$where."and clan_id=$clan_selected";
}
$today=date("Y-m-d");

$players=array();
$timet=microtime(true)*1;
if ($today==$time) {
    if ($clan_selected<0) {
        $all_clans=GetClans();
        // print_r($all_clans);
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
        // echo "here2";
    } else {
        $all_clans=GetClans();
        // echo "here";
        // print_r($clans);
        // print_r($all_clans);
        foreach ($all_clans as $clan) {
            if ($clan["id"]==$clan_selected) {
                // $pll=GetClanData($clan["id"]);
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
        }
    }
    // if ($order=="nick") {
    //     $ord=1;
    // }
    // if ($order=="frags") {
    //     $ord=2;
    // }
    // if ($order=="deaths") {
    //     $ord=3;
    // }
    // if ($order=="level") {
    //     $ord=4;
    // }
    // if ($order_way=="desc") {
    //     for ($a=0;$a<count($players);$a++) {
    //         for ($b=0;$b<count($players);$b++) {
    //             if ($players[$a][$ord]>$players[$b][$ord]) {
    //                 $tmp=$players[$a];
    //                 $players[$a]=$players[$b];
    //                 $players[$b]=$tmp;
    //             }
    //         }
    //     }
    // } else {
    //     for ($a=0;$a<count($players);$a++) {
    //         for ($b=0;$b<count($players);$b++) {
    //             if ($players[$a][$ord]<$players[$b][$ord]) {
    //                 $tmp=$players[$a];
    //                 $players[$a]=$players[$b];
    //                 $players[$b]=$tmp;
    //             }
    //         }
    //     }
    // }
} else {
    $query = "\nSELECT * FROM {$config["base_database"]}.Players WHERE timemark=\"$time\" $where ORDER BY id asc;\n";

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
        // $name=GetClanName2($connection, $config, $row["clan_id"]);
        // echo "<td>$name</td>";
        // echo "</tr>";
        }
    }
}
// print_r($players);
// echo "</table>";
// echo microtime(true)*1-$timet."<br><br>";
$timet=microtime(true)*1;
$time2=date("Y-m-d", strtotime("$time 00:00:00") - 60 * 60 * 24);
if (CheckDatee($connection, $config, $time2)==-1) {
    // echo strtotime("$time 00:00:00");
    $time2=$time;
}
$query = "\nSELECT * FROM {$config["base_database"]}.Players WHERE timemark=\"$time2\" $where ORDER BY id asc;\n";

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
        // $name=GetClanName2($connection, $config, $row["clan_id"]);
        // echo "<td>$name</td>";
        // echo "</tr>";
    }
}
// echo microtime(true)*1-$timet."<br><br>";
$timet=microtime(true)*1;
$lbz=array();
foreach ($eras as $era) {
    if ($era_selected==$era[0]) {
        $time3=$era[1];
        $lbz1=explode(";", $era[3]);
        foreach ($lbz1 as $lb) {
            $tmp=explode("=", $lb);
            array_push($lbz, $tmp);
        }
    }
}
// $query = "\nSELECT * FROM {$config["base_database"]}.Eras ORDER BY started DESC limit 1;\n";
// $result = $connection->query($query);
// // echo $query;
// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         // print_r($row);
//         $time3=$row["started"];
//         $lbz1=explode(";", $row["lbz"]);
//         foreach ($lbz1 as $lb) {
//             $tmp=explode("=", $lb);
//             array_push($lbz, $tmp);
//         }
//     }
// }
// print_r($lbz);
// echo $time3;
$players3=array();

// foreach ($players as $pl) {
    $query = "\nSELECT * FROM {$config["base_database"]}.Players WHERE timemark>=\"$time3\" $where and timemark<=\"$time\" ORDER BY timemark asc;\n";

    // echo $query;
    $result = $connection->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $was=0;
            foreach ($players3 as $pl) {
                // code...
                if ($pl[0]==$row["id"]) {
                    $was=1;
                }
            }
            if ($was==0) {
                $tmp=array();
                array_push($tmp, $row["id"]);
                array_push($tmp, $row["nick"]);
                array_push($tmp, $row["frags"]);
                array_push($tmp, $row["deaths"]);
                array_push($tmp, $row["level"]);
                array_push($tmp, $row["clan_id"]);
                array_push($players3, $tmp);
            }
            // print_r($row);
        // echo "<tr>";
        // echo "<td>{$row["nick"]}</td>";
        // echo "<td>{$row["frags"]}</td>";
        // echo "<td>{$row["deaths"]}</td>";
        // echo "<td>{$row["level"]}</td>";
        // $name=GetClanName2($connection, $config, $row["clan_id"]);
        // echo "<td>$name</td>";
        // echo "</tr>";
        }
    }
// }

// echo microtime(true)*1-$timet."<br><br>";
$timet=microtime(true)*1;

// echo "<table>
// <th align=\"center\"> Никнейм </th>
// <th align=\"center\"> Фраги </th>
// <th align=\"center\">Δ Фрагов </th>
// <th align=\"center\"> Смерти </th>
// <th align=\"center\">Δ Смертрей </th>
// <th align=\"center\"> Уровень </th>
// <th align=\"center\"> Клан </th>
// </tr>";
echo "<table style=\"float: left\">";
//<th align=\"center\"> № </th>
echo "<th align=\"center\"> Никнейм </th>
<th align=\"center\"> Фраги </th>
<th align=\"center\"> Смерти </th>
<th align=\"center\"> Уровень </th>
<th align=\"center\"> Клан </th>";
if ($era_selected!=-1) {
    echo "<th align=\"center\"> Фраги<br>в эре </th>
<th align=\"center\"> Смерти<br>в эре </th>
<th align=\"center\"> Содары </th>
<th align=\"center\"> Участия </th>
<th align=\"center\"> Очки </th>
<th align=\"center\"> ЛБЗ </th>";
}
echo "</tr>";
$cnt=1;
$rowws=array();
foreach ($players as $pl1) {
    foreach ($players3 as $pl2) {
        if ($pl1[0]==$pl2[0]) {
            if ($nickname!=null) {
                $pos2 = stripos($pl1[1], $nickname);
                if ($pos2!== false) {
                    $nickname2=substr($pl1[1], 0, $pos2)."<b>".substr($pl1[1], $pos2, strlen($nickname))."</b>".substr($pl1[1], (strlen($nickname)+$pos2), strlen($pl1[1]));
                    $tmp=array();
                    // echo " $pl1[0]==$pl2[0]";
                    // echo "<tr>";
                    // echo "<td>{$pl1[1]}</td>";
                    array_push($tmp, $cnt);
                    // echo "<td align=\"center\">$cnt</td>";
                    $cnt++;
                    array_push($tmp, "<p><a href=\"/player.php?id={$pl1[0]}\">{$nickname2}</a></p>");
                    // echo "<td><p><a href=\"/test.php?nick={$pl1[1]}\">{$pl1[1]}</a></p></td>";
                    array_push($tmp, $pl1[2]);
                    // echo "<td>{$pl1[2]}</td>";
                    // // $a=$pl1[2]-$pl2[2];
                    // echo "<td>$a</td>";
                    array_push($tmp, $pl1[3]);
                    // echo "<td>{$pl1[3]}</td>";
                    // // $b=$pl1[3]-$pl2[3];
                    // // echo "<td>$b</td>";
                    array_push($tmp, $pl1[4]);
                    // echo "<td>{$pl1[4]}</td>";
                    $name=GetClanName2($connection, $config, $clans, $pl1[5]);
                    array_push($tmp, $name);
                    // echo "<td>$name</td>";
                    $a=$pl1[2]-$pl2[2];
                    array_push($tmp, $a);
                    // echo "<td>$a</td>";
                    $b=$pl1[3]-$pl2[3];
                    array_push($tmp, $b);
                    // echo "<td>$b</td>";
                    $c=floor(2*$a+0.5*$b);
                    array_push($tmp, $c);
                    // echo "<td>$c</td>";
                    $u=$a+$b;
                    array_push($tmp, $u);
                    // echo "<td>$u</td>";
                    $o=5*$a+$b;
                    array_push($tmp, $o);
                    // echo "<td>$o</td>";
                    $lbzz="";
                    foreach ($lbz as $lb) {
                        if ($lb[0]<=$u) {
                            $lbzz=$lb[1];
                        }
                    }
                    array_push($tmp, "<div style=\"word-wrap: break-word;\">$lbzz</div>");
                    // echo "<td><div style=\"word-wrap: break-word;\">$lbzz</div></td>";
                    // echo "</tr>";
                    array_push($rowws, $tmp);
                }
            } else {
                $tmp=array();
                // echo " $pl1[0]==$pl2[0]";
                // echo "<tr>";
                // echo "<td>{$pl1[1]}</td>";
                array_push($tmp, $cnt);
                // echo "<td align=\"center\">$cnt</td>";
                $cnt++;
                array_push($tmp, "<p><a href=\"/player.php?id={$pl1[0]}\">{$pl1[1]}</a></p>");
                // echo "<td><p><a href=\"/test.php?nick={$pl1[1]}\">{$pl1[1]}</a></p></td>";
                array_push($tmp, $pl1[2]);
                // echo "<td>{$pl1[2]}</td>";
                // // $a=$pl1[2]-$pl2[2];
                // echo "<td>$a</td>";
                array_push($tmp, $pl1[3]);
                // echo "<td>{$pl1[3]}</td>";
                // // $b=$pl1[3]-$pl2[3];
                // // echo "<td>$b</td>";
                array_push($tmp, $pl1[4]);
                // echo "<td>{$pl1[4]}</td>";
                $name=GetClanName2($connection, $config, $clans, $pl1[5]);
                array_push($tmp, $name);
                // echo "<td>$name</td>";
                $a=$pl1[2]-$pl2[2];
                array_push($tmp, $a);
                // echo "<td>$a</td>";
                $b=$pl1[3]-$pl2[3];
                array_push($tmp, $b);
                // echo "<td>$b</td>";
                $c=floor(2*$a+0.5*$b);
                array_push($tmp, $c);
                // echo "<td>$c</td>";
                $u=$a+$b;
                array_push($tmp, $u);
                // echo "<td>$u</td>";
                $o=5*$a+$b;
                array_push($tmp, $o);
                // echo "<td>$o</td>";
                $lbzz="";
                foreach ($lbz as $lb) {
                    if ($lb[0]<=$u) {
                        $lbzz=$lb[1];
                    }
                }
                array_push($tmp, "<div style=\"word-wrap: break-word;\">$lbzz</div>");
                // echo "<td><div style=\"word-wrap: break-word;\">$lbzz</div></td>";
                // echo "</tr>";
                array_push($rowws, $tmp);
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
if ($order=="clan") {
    $ord=5;
}
if ($order=="fragse") {
    $ord=6;
}
if ($order=="deathse") {
    $ord=7;
}
if ($order=="sodars") {
    $ord=8;
}
if ($order=="actions") {
    $ord=9;
}
if ($order=="points") {
    $ord=10;
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
foreach ($rowws as $row) {
    // print_r($row);
    echo "<tr>";
    //echo "<td>$row[0]</td>";
    echo "<td>$row[1]</td>";
    echo "<td>$row[2]</td>";
    echo "<td>$row[3]</td>";
    echo "<td>$row[4]</td>";
    echo "<td>$row[5]</td>";
    if ($era_selected!=-1) {
        echo "<td>$row[6]</td>";
        echo "<td>$row[7]</td>";
        echo "<td>$row[8]</td>";
        echo "<td>$row[9]</td>";
        echo "<td>$row[10]</td>";
        echo "<td>$row[11]</td>";
    }
    echo "</tr>";
}
echo "</table>";

echo "<table style=\"float: left\">
<tr>
<th colspan=\"4\">Переходы игроков за [$time2 - $time]</th>
</tr>
<th align=\"center\"> № </th>
<th align=\"center\"> Никнейм </th>
<th align=\"center\"> Покинул клан </th>
<th align=\"center\"> Вступил клан </th>
</tr>";

$left=array();
$names=array();
// print_r($players);
foreach ($players as $pl1) {
    $was=0;
    foreach ($players2 as $pl2) {
        if ($pl1[0]==$pl2[0]) {
            $was=1;
            // echo " $pl1[0]==$pl2[0]";
            if ($pl1[1]!=$pl2[1]) {
                $tmp=array();
                array_push($tmp, $pl1[0]);
                array_push($tmp, $pl2[1]);
                array_push($tmp, $pl1[1]);
                array_push($names, $tmp);
            }
            if ($pl1[5]!=$pl2[5]) {
                // echo " $pl1[0]==$pl2[0]";
                $name=GetClanName2($connection, $config, $clans, $pl1[5]);
                $name2=GetClanName2($connection, $config, $clans, $pl2[5]);
                $tmp=array();
                array_push($tmp, $pl1[0]);
                array_push($tmp, $pl1[1]);
                array_push($tmp, $name2);
                array_push($tmp, $name);
                array_push($left, $tmp);
            }
        }
    }
    if ($was==0) {
        $name=GetClanName2($connection, $config, $clans, $pl1[5]);
        $tmp=array();
        array_push($tmp, $pl1[0]);
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
        $name=GetClanName2($connection, $config, $clans, $pl1[5]);
        $tmp=array();
        array_push($tmp, $pl1[0]);
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
        echo "<td><p><a href=\"/player.php?id={$pl[0]}\">{$pl[1]}</a></p></td>";
        echo "<td>{$pl[2]}</td>";
        echo "<td>{$pl[3]}</td>";
        echo "</tr>";
    }
}

echo "</table>";
// echo microtime(true)*1-$timet."<br><br>";

echo "<table style=\"float: left\">
<tr>
<th colspan=\"3\">Смена ников за [$time2 - $time]</th>
</tr>
<th align=\"center\"> № </th>
<th align=\"center\"> Старый </th>
<th align=\"center\"> Новый </th>
</tr>";
$cnt=1;
foreach ($names as $nm) {
    echo "<tr>";
    // echo "<td>{$pl[0]}</td>";
    echo "<td align=\"center\">$cnt</td>";
    $cnt++;
    echo "<td><p><a href=\"/player.php?id={$nm[0]}\">{$nm[1]}</a></p></td>";
    echo "<td><p><a href=\"/player.php?id={$nm[0]}\">{$nm[2]}</a></p></td>";
    // echo "<td>{$nm[0]}</td>";
    // echo "<td>{$nm[1]}</td>";
    echo "</tr>";
}
if (count($names)==0) {
    echo "<tr>";
    echo "<td colspan=\"3\">Нет Данных</td>";
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
