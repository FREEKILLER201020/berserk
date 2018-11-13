<?php
function scan($path)
{
    $file  = file_get_contents(realpath(dirname(__FILE__))."/config.json");
    $config = json_decode($file, true);
    $connection=Connect($config);
    $colors = new Colors();
    ini_set('memory_limit', '4096M');
    $str_time=microtime(true);
    $pref="THE_DATA/DATA/";
    if ($path!=null) {
        $pref=$path;
    }
    $data=array();
    $killers=array();
    $deads=array();
    // echo "\n $pref \n";
    $lines = shell_exec("ls $pref");
    $lines=explode("\n", $lines);
    unset($lines[count($lines)-1]);
    // print_r($lines);
    $po=0;
    echo "\nGet data\n";
    // if ($save==1) {
    $t0=microtime(true)*10000;
    $cnt=0;
    foreach ($lines as $line) {
        $cnt++;
        $t=(microtime(true)*10000-$t0)/$cnt;
        progressBar($po++, count($lines)-1, $t, $t0);
        // for ($k=1000;$k<1200;$k++) {
//     $line=$lines[$k];
        // echo $k."\n";
        $tmp=explode('-', $line);
        // echo $line;
        // print_r($tmp);
        $tmp2=explode(':', $tmp[5]);
        if ($tmp[3]=="January") {
            $month=1;
        } elseif ($tmp[3]=="February") {
            $month=2;
        } elseif ($tmp[3]=="March") {
            $month=3;
        } elseif ($tmp[3]=="April") {
            $month=4;
        } elseif ($tmp[3]=="May") {
            $month=5;
        } elseif ($tmp[3]=="June") {
            $month=6;
        } elseif ($tmp[3]=="July") {
            $month=7;
        } elseif ($tmp[3]=="August") {
            $month=8;
        } elseif ($tmp[3]=="September") {
            $month=9;
        } elseif ($tmp[3]=="October") {
            $month=10;
        } elseif ($tmp[3]=="November") {
            $month=11;
        } elseif ($tmp[3]=="December") {
            $month=12;
        }

        // print_r($tmp);
        // print_r($tmp2);
        // echo $tmp[6][0];
        $add=0;
        // echo strpos($tmp[6], "PM");
        if ($tmp[6][0]=="P") {
            $add=12;
        }
        // echo $add;
        if ($tmp2[0]==12) {
            $h=0;
        } else {
            $h=$tmp2[0];
        }
        // print_r($tmp2);
        $date=((int)$tmp[1]+$month*31+(int)$tmp[4]*366)*24*60*60+($h+$add)*60*60+$tmp2[1]*60+$tmp2[2];
        // echo "\n\n\n",$date,"\n\n\n";
        // echo "\n".((int)$tmp[1]+$month*31+(int)$tmp[4]*366)*24*60*60.0."\n";
        // echo "\n".($tmp2[0]+$add)*60*60+$tmp2[1]*60+$tmp2[3]."\n";
        // echo "\n".$date."\n";
        array_push($data, ["line"=>$line,"time"=>$date,"pl"=>null]);
    }

    // print_r($data);
    // exit();


    echo "\nSort data\n";
    $t=0;
    $t0=microtime(true)*10000;
    $cnt=0;
    $data_q=$data;
    for ($i=0;$i<count($data)-1;$i++) {
        // echo $i."\n";
        $cnt++;
        $t=(microtime(true)*10000-$t0)/$cnt;
        // $t=Delta($t);
        progressBar($i, count($data)-2, $t, $t0);

        for ($j=$i+1;$j<count($data);$j++) {
            if ($i!=$j) {
                // echo $data[$i]["time"],"\n";
                if ($data[$i]["time"]>=$data[$j]["time"]) {
                    $tmp=$data[$i];
                    $data[$i]=$data[$j];
                    $data[$j]=$tmp;
                }
            }
        }
    }
    //
    // echo "\nQuick sort\n";
    // $t0=microtime(true)*10000;
    // qsort($data_q);
    // $spent=microtime(true)*10000-$t0;
    // $sec2=$spent/10000;
    // $mil2=(int)$spent%10000;
    // $min2=intval($sec2/60);
    // $sec2=(int)$sec2%60;
    // echo "\nTime spend: ".$min2." min ".$sec2." sec ".$mil2." mil\n";
    //
    // // print_r($data);
    // exit();
//
    $kills=0;
    $deaths=0;
    $err=0;
    print_r($data);
    // exit();
    echo "\nGet killers and deads\n";
    $start=0;
    $events=array();
    $end=count($data);
    $current_data=array();
    $new_data=array();
    $cnt=0;
    $t0=microtime(true)*10000;
    // $current_data=GetPlayers($data, $start, $pref);
    // while ($current_data==null) {
    //     $cnt++;
    //     $t=(microtime(true)*10000-$t0)/$cnt;
    //     progressBar($start+1, $end-2, $t, $t0);
    //     $start++;
    //     $current_data=GetPlayers($data, $start, $pref);
    //     // exit();
    // }
    // // exit();
    $lns=array();
    for ($po=$start;$po<$end;$po++) {
        $was3=0;
        $cnt++;
        $t=(microtime(true)*10000-$t0)/$cnt;
        progressBar($po, $end-2, $t, $t0);
        $new_data=GetPlayers($data, $po, $pref);
        $fights=GetFightsData($data, $po, $pref);
        $clans=GetClansData($data, $po, $pref);




        // $date=date("Y-m-d", time());
        // $query="SELECT * FROM {$config["base_database"]}.Players WHERE timemark=\"$date\"";
        // echo $query;
        // $result = $connection->query($query);
        // if ($result->num_rows <= 0) {
        //     $clans=GetClans();
        //     print_r($clans);
        //     foreach ($clans as $clan) {
        //         if ($clan["points"]==0) {
        //             $points="null";
        //         } else {
        //             $points=$clan["points"];
        //         }
        //         $title=Restring($clan["title"]);
        //         // $query="SELECT * FROM {$config["base_database"]}.Clans WHERE id={$clan["id"]} and timemark=\"$date\"";
        //         // echo $query;
        //         // $result = $connection->query($query);
        //         // print_r($result);
        //         // if ($result->num_rows <= 0) {
        //         $query = "INSERT INTO {$config["base_database"]}.Clans (timemark,id,title,points) VALUES (\"$date\",{$clan["id"]},'$title',$points);\n";
        //         echo $query;
        //         $result = $connection->query($query);
        //         if (!$result) {
        //             die("Error during filling clans table".$connection->connect_errno.$connection->connect_error);
        //         }
        //         // } else {
        //         //     $query = "UPDATE {$config["base_database"]}.Clans SET title='$title' ,points=$points WHERE id={$clan["id"]}\n";
        //         //     echo $query;
        //         //     $result = $connection->query($query);
        //         //     if (!$result) {
        //         //         die("Error during filling clans table".$connection->connect_errno.$connection->connect_error);
        //         //     }
        //         // }
        //         $players=GetClanData($clan["id"]);
        //         print_r($players);
        //         foreach ($players as $key=> $data) {
        //             echo "$key => $data";
        //             if ($key=="players") {
        //                 foreach ($data as $player) {
        //                     print_r($player);
        //                     $nick=Restring($player["nick"]);
        //                     $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";
        //                     echo $query;
        //                     $result = $connection->query($query);
        //                     if (!$result) {
        //                         die("Error during filling players table".$connection->connect_errno.$connection->connect_error);
        //                     }
        //                 }
        //             }
        //         }
        //     }
        //     $attacks=GetFights();
        //     print_r($attacks);
        //     foreach ($attacks as $attack) {
        //         $query="SELECT * FROM {$config["base_database"]}.Attacks WHERE attacker=".GetClanID($connection, $config, $attack["attacker"]).", defender=".GetClanID($connection, $config, $attack["defender"]).",declared={$attack["attacker"]},resolved={$attack["resolved"]}";
        //         echo $query;
        //         $result = $connection->query($query);
        //         if ($result->num_rows <= 0) {
        //             $attacker=GetClanID($connection, $config, $attack["attacker"], $date);
        //             $defender=GetClanID($connection, $config, $attack["defender"], $date);
        //             $query = "INSERT INTO {$config["base_database"]}.Attacks (fromm,too,attacker,defender,declared,resolved) VALUES (\"{$attack["from"]}\",\"{$attack["to"]}\",$attacker,$defender,\"{$attack["declared"]}\",\"{$attack["resolved"]}\");\n";
        //             echo $query;
        //             $result = $connection->query($query);
        //             if (!$result) {
        //                 die("Error during filling cities table".$connection->connect_errno.$connection->connect_error);
        //             }
        //         }
        //     }
        // }




        if (count($fights)<1) {
            echo $colors->getColoredString("\n NO FIGHTS \n", "red", null);
        } else {
            echo $colors->getColoredString("\n HAS FIGHTS \n", "green", null);
        }
        // if ($new_data!=null) {
        $tmp_killers=array();
        $tmp_deads=array();
        for ($i=0;$i<count($new_data);$i++) {
            for ($j=0;$j<count($current_data);$j++) {
                if ($new_data[$i]->id==$current_data[$j]->id) {
                    if (((($new_data[$i]->frags-$current_data[$j]->frags)>0)|| (($new_data[$i]->deaths-$current_data[$j]->deaths)>0)) && ($was3==0)) {
                        array_push($lns, $po-1);
                        $was3=1;
                    }
                    if ($new_data[$i]->frags>$current_data[$j]->frags) {
                        for ($p=1;$p<=($new_data[$i]->frags-$current_data[$j]->frags);$p++) {
                            array_push($tmp_killers, $new_data[$i]->id);
                        }
                    }
                    if ($new_data[$i]->deaths>$current_data[$j]->deaths) {
                        for ($p=1;$p<=($new_data[$i]->deaths-$current_data[$j]->deaths);$p++) {
                            array_push($tmp_deads, $new_data[$i]->id);
                        }
                    }
                    $current_data[$j]=$new_data[$i];
                }
            }
            // }
        }
        foreach ($tmp_killers as $killer) {
            array_push($killers, $killer);
        }
        foreach ($tmp_deads as $dead) {
            array_push($deads, $dead);
        }
        // print_r($tmp_killers);
        // print_r($tmp_deads);
        if ((count($tmp_killers)==(count($tmp_deads)))&&(count($tmp_killers)==1)) {
            // echo $colors->getColoredString("\n $tmp_killers[0] killed $tmp_deads[0] \n", "green", null);
            array_push($events, new Kill($tmp_killers[0], $tmp_deads[0]));
            if (($conf!=null)&&((count($conf->killers)>0)||(count($conf->deads)>0))) {
                array_push($events, $conf);
                $conf=null;
            }
        } else {
            if ($conf==null) {
                $conf=new Conflict;
            }
            foreach ($tmp_killers as $killer) {
                array_push($conf->killers, $killer);
            }
            foreach ($tmp_deads as $dead) {
                array_push($conf->deads, $dead);
            }
        }
        // echo "\n Killers: ",count($killers)," Deads: ",count($deads),"\n";
    }
    // print_r($events);
    $e_killer=array();
    $e_deads=array();
    foreach ($events as $event) {
        if ($event instanceof Kill) {
            array_push($e_killer, $event->killer);
            array_push($e_deads, $event->dead);
        }
        if ($event instanceof Conflict) {
            foreach ($event->killers as $kill) {
                array_push($e_killer, $kill);
            }
            foreach ($event->deads as $dead) {
                array_push($e_deads, $dead);
            }
        }
    }
    echo "\nTime spent: ",gmdate("H:i:s", microtime(true)-$str_time),"\n";
    echo "\nKillers: ",count($killers),"\nDeads: ",count($deads);
    echo "\nEvent killers: ",count($e_killer),"\nEvent deads: ",count($e_deads),"\n";
    // $jsn=json_encode($events);
    eval('$b = ' . var_export($events, true) . ';');

    var_dump($b);
    file_put_contents("all00.php", serialize($b));
}
// exit();
// print_r($data[0]);
// $start=0;
// // $end=700;
// $end=count($data);
// $dt2=$data[$start]["pl"];
// $ofset=0;
// // echo "\na\n";
// // print_r($dt2);
// // exit();
// echo "\nsetp4\n";
// for ($po=$start;$po<$end-1;$po++) {
//     // echo $po."\n";
//     $t3=microtime(true)*10000;
//     $t2=(round($t3-$t)+$prev)/2;
//     $t=microtime(true)*10000;
//     $prev=round($t3-$t);
//     progressBar($po, $end-2, $t2);
//     $k1=$kills;
//     $d1=$deaths;
//     $dt1=$dt2;
//
//     $dt2=$data[$po+1]["pl"];
//     // print_r($dt1);
//     // exit();
//     $tmp=$kills;
//     $clans=array();
//     $was1=1;
//     $was2=1;
//     // echo "\n!!!".$dt1[0]->id,"!!!\n";
//     // exit();
//     foreach ($dt1 as $pl1) {
//         $was=1;
//         // echo $dt1[0]->id,"\n";
//         foreach ($dt2 as $pl2) {
//             if ($pl1->id == $pl2->id) {
//                 $was=0;
//                 $kills+=$pl2->frags-$pl1->frags;
//                 $deaths+=$pl2->deaths-$pl1->deaths;
//                 if (($pl2->frags-$pl1->frags !=0)||($pl2->deaths-$pl1->deaths != 0)) {
//                     foreach ($clans as $clan) {
//                         if ($clan==$pl1->clan_id) {
//                             $was1=0;
//                         }
//                         if ($clan==$pl2->clan_id) {
//                             $was2=0;
//                         }
//                     }
//                     if ($was1==1) {
//                         array_push($clans, $pl1->clan_id);
//                     }
//                     if ($was2==1) {
//                         array_push($clans, $pl2->clan_id);
//                     }
//                     // print_r($pl1);
//                     // print_r($pl2);
//                 }
//             }
//         }
//         // if ($was==1) {
//         //     echo "\n $pl1->nick has no pare\n";
//         // }
//     }
//     $killers_over=$kills-$k1;
//     $deaths_over=$deaths-$d1;
//     // echo "\n $killers_over  $deaths_over  \n";
//     if ($deaths_over>$killers_over) {
//         $dofset++;
//         echo "\n $killers_over  $deaths_over  $po\n";
//     }
//     if ($killers_over>$deaths_over) {
//         $kofset++;
//         echo "\n $killers_over  $deaths_over  $po\n";
//     }
//     $clans=array_unique($clans);
//     if (count($clans)>=3) {
//         $cros++;
//     }
//     if ($kills-$tmp>=2) {
//         $coll++;
//     }
//     if ($kills != $deaths) {
//         // echo "\n error on $po step \n";
//         // print_r($dt1);
//         // print_r($dt2);
//         $err++;
//         // exit();
//     }
//     // break;
//     // $data[$po]["pl"]=$pl1;
// }
// echo "\nkills->$kills ; frags->$deaths ; err->$err ; col->$coll ; cros->$cros ; kofset->$kofset ; dofset->$dofset\n";



// echo $jsn;


function GetPlayers($data, $po, $pref)
{
    $row=$data[$po];
    $tmp=$pref;
    $tmp.=$row["line"];
    $tmp.="/";
    $ln1=$tmp;
    // echo $ln1;
    $lines = shell_exec("ls $tmp");
    $lines=explode("\n", $lines);
    // print_r($lines);
    unset($lines[count($lines)-1]);
    $data_t=array();
    for ($i=count($lines)-1;$i>=0;$i--) {
        $line=$lines[$i];
        if (strpos($line, "clan[")>=-1) {
            $tmp=explode('_', $line);
            array_push($data_t, ["clan"=>substr($tmp[0], strpos($tmp[0], "[")+1, strpos($tmp[0], "]")-strpos($tmp[0], "[")-1),"file"=>$lines[$i]]);
        }
        // if (strpos($line, "fights")>=-1) {
        //     $file = shell_exec("cat $ln1$line");
        //     $json = json_decode($file, true);
        //     // print_r($json);
        //     if ($json==null) {
        //         return null;
        //     }
        // }
    }
    $pl1=array();
    foreach ($data_t as $dt) {
        $dta=GetClanData2($ln1.str_replace("[", "\[", str_replace("]", "\]", $dt["file"])));
        if ($dta["players"]!=null) {
            foreach ($dta["players"] as $player_tmp) {
                $pl=new D_Player($player_tmp["id"], $player_tmp["nick"], $player_tmp["frags"], $player_tmp["deaths"], $player_tmp["level"], $dt["clan"]);
                array_push($pl1, $pl);
            }
        }
    }
    // exit();

    return $pl1;
}





// function GetPlayers($data, $po, $pref)
// {
//     $row=$data[$po];
//     // $row=$row1["line"];
//     // print_r($row);
//     $tmp=$pref;
//     $tmp.=$row["line"];
//     $tmp.="/";
//     $ln1=$tmp;
//     // echo "\n".$tmp."\n";
//
//     $lines = shell_exec("ls $tmp");
//     $lines=explode("\n", $lines);
//     unset($lines[count($lines)-1]);
//     // print_r($lines);
//     $data_t=array();
//     // foreach ($lines as $line) {
//     for ($i=0;$i<count($lines);$i++) {
//         $line=$lines[$i];
//         // echo $line."\n";
//         // echo strpos($line, "clan[")."\n";
//         if (strpos($line, "clan[")>=-1) {
//             // echo $line."is a line \n";
//             $tmp=explode('_', $line);
//             // print_r($tmp);
//             array_push($data_t, ["clan"=>substr($tmp[0], strpos($tmp[0], "[")+1, strpos($tmp[0], "]")-strpos($tmp[0], "[")-1),"file"=>$lines[$i]]);
//         }
//     }
//     // print_r($data_t);
//
//     $pl1=array();
//     foreach ($data_t as $dt) {
//         // print_r($dt);
//         $dta=GetClanData2($ln1.str_replace("[", "\[", str_replace("]", "\]", $dt["file"])));
//         // print_r($dta);
//         if (count($dta["players"])>0) {
//             foreach ($dta["players"] as $player_tmp) {
//                 $pl=new D_Player($player_tmp["id"], $player_tmp["nick"], $player_tmp["frags"], $player_tmp["deaths"], $player_tmp["level"], $dt["clan"]);
//                 array_push($pl1, $pl);
//                 // print_r($pl);
//             }
//         }
//     }
//     // print_r($pl1);
//     return $pl1;
// }


function GetFightsData($data, $po, $pref)
{
    $row=$data[$po];
    // $row=$row1["line"];
    // print_r($row);
    $tmp=$pref;
    $tmp.=$row["line"];
    $tmp.="/";
    $ln1=$tmp;
    echo "\n".$tmp."\n";

    $lines = shell_exec("ls $tmp");
    $lines=explode("\n", $lines);
    unset($lines[count($lines)-1]);
    // print_r($lines);
    $data_t=array();
    // foreach ($lines as $line) {
    for ($i=0;$i<count($lines);$i++) {
        $line=$lines[$i];
        // echo $line."\n";
        // echo strpos($line, "clan[")."\n";
        if (strpos($line, "fights")>=-1) {
            // echo $tmp.$line."is a line \n";
            // $tmp=explode('_', $line);
            // print_r($tmp);
            // array_push($data_t, ["clan"=>substr($tmp[0], strpos($tmp[0], "[")+1, strpos($tmp[0], "]")-strpos($tmp[0], "[")-1),"file"=>$lines[$i]]);
            $file=file_get_contents($tmp.$line);
            $json = json_decode($file, true);
            // print_r($json);
            return $json;
        }
    }
}

    function GetClansData($data, $po, $pref)
    {
        $row=$data[$po];
        // $row=$row1["line"];
        // print_r($row);
        $tmp=$pref;
        $tmp.=$row["line"];
        $tmp.="/";
        $ln1=$tmp;
        // echo "\n".$tmp."\n";

        $lines = shell_exec("ls $tmp");
        $lines=explode("\n", $lines);
        unset($lines[count($lines)-1]);
        // print_r($lines);
        $data_t=array();
        // foreach ($lines as $line) {
        for ($i=0;$i<count($lines);$i++) {
            $line=$lines[$i];
            // echo $line."\n";
            // echo strpos($line, "clan[")."\n";
            if (strpos($line, "clans")>=-1) {
                // echo $tmp.$line."is a line \n";
                // $tmp=explode('_', $line);
                // print_r($tmp);
                // array_push($data_t, ["clan"=>substr($tmp[0], strpos($tmp[0], "[")+1, strpos($tmp[0], "]")-strpos($tmp[0], "[")-1),"file"=>$lines[$i]]);
                $file=file_get_contents($tmp.$line);
                $json = json_decode($file, true);
                // print_r($json);
                return $json;
            }
        }
        // print_r($data_t);

    // $pl1=array();
    // foreach ($data_t as $dt) {
    //     // print_r($dt);
    //     $dta=GetClanData2($ln1.str_replace("[", "\[", str_replace("]", "\]", $dt["file"])));
    //     // print_r($dta);
    //     if (count($dta["players"])>0) {
    //         foreach ($dta["players"] as $player_tmp) {
    //             $pl=new D_Player($player_tmp["id"], $player_tmp["nick"], $player_tmp["frags"], $player_tmp["deaths"], $player_tmp["level"], $dt["clan"]);
    //             array_push($pl1, $pl);
    //             // print_r($pl);
    //         }
    //     }
    // }
    // print_r($pl1);
    // return $pl1;
    }



// print_r($data);

// echo "\nsetp4\n";
// for ($po=$start;$po<$end-1;$po++) {
//     // for ($po=0;$po<count($data)-1;$po++) {
//     // echo $po."\n";
//     progressBar($po, count($data));
//     $dt1=$data[$po];
//     $dt2=$data[$po+1];
//     foreach ($dt1["pl"] as $pl1) {
//         foreach ($dt2["pl"] as $pl2) {
//             if ($pl1->id == $pl2->id) {
//                 $kills+=$pl2->frags-$pl1->frags;
//                 $deaths+=$pl2->deaths-$pl1->deaths;
//                 // if (($pl2->frags-$pl1->frags !=0)||($pl2->deaths-$pl1->deaths != 0)) {
//                 //     print_r($pl1);
//                 //     print_r($pl2);
//                 // }
//             }
//         }
//     }
//     if ($kills != $deaths) {
//         $err++;
//     }
//     // echo "kills->$kills ; frags->$deaths ; err->$err \n";
// }




function GetClanData2($row)
{
    // echo $row;

    $file = shell_exec("cat $row");
    $json = json_decode($file, true);
    // print_r($file);
    // echo $file.$row;
    return $json;
}


function Delta($t)
{
    $t2=microtime(true)*10000;
    $t=($t+$t2)/2;
    return $t;
}


function progressBar($done, $total, $step_time, $start_time)
{
    $perc = floor(($done / $total) * 10);
    $perc2 = floor(($done / $total) * 100);
    $left = 10 - $perc;
    $spent=microtime(true)*10000-$start_time;
    $sec2=$spent/10000;
    $mil2=(int)$spent%10000;
    $min2=intval($sec2/60);
    $sec2=(int)$sec2%60;
    $mil=(($total-$done)*$step_time);
    $sec=$mil/10000;
    $mil=(int)$mil%10000;
    $min=intval($sec/60);
    $sec=(int)$sec%60;
    $write = sprintf("\033[0G\033[2K[%'={$perc}s>%-{$left}s] - $perc2%% - $done/$total; Time spend: ".$min2." min ".$sec2." sec ".$mil2." mil". "; Time left: ".$min." min ".$sec." sec ".$mil." mil", "", "");
    fwrite(STDERR, $write);
}



class D_Player
{
    public $id; // id игрока
    public $nick; // ник игрока
    public $frags; // количество его фрагов
    public $deaths; // количество его смертей
    public $level; // его уровень (пока не используется)
    public $clan_id; // id клана, в котором состоит игрок

    public function __construct($i, $n, $f, $d, $l, $c)
    {
        $this->id=$i;
        $this->nick=$n;
        $this->frags=$f;
        $this->deaths=$d;
        $this->level=$l;
        $this->clan_id=$c;
    }
    public function Update($pl)
    {
        $ret=["frags"=>$pl->frags-$this->frags,"deaths"=>$pl->deaths-$this->deaths];
        $this->nick=$pl->nick;
        $this->frags=$pl->frags;
        $this->deaths=$pl->deaths;
        $this->level=$pl->level;
        return $ret;
    }
    public function CP()
    {
        $pl=new D_Player($this->id, $this->nick, $this->frags, $this->deaths, $this->level, $this->clan_id);
        return $pl;
    }
}


class Conflict
{
    public $killers=array();
    public $deads=array();
    public static function __set_state($an_array) // С PHP 5.1.0
    {
        $obj = new Conflict;
        $obj->killers = $an_array['killers'];
        $obj->deads = $an_array['deads'];
        return $obj;
    }
}
class Kill
{
    public $killer;
    public $dead;

    public function __construct($k, $d)
    {
        $this->killer=$k;
        $this->dead=$d;
    }

    public static function __set_state($an_array) // С PHP 5.1.0
    {
        $obj = new Kill($an_array['killer'], $an_array['dead']);
        // $obj->killer = $an_array['killer'];
        // $obj->dead = $an_array['dead'];
        return $obj;
    }
}
// qsort($array);

/*
* Функция вычисляет количество элементов,
* тем самым подготавливая параметры для первого запуска,
* и запускает сам процесс.
*/

function qsort(&$array)
{
    $left = 0;
    $right = count($array) - 1;

    my_sort($array, $left, $right);
}

/*
* Функция, непосредственно производящая сортировку.
* Так как массив передается по ссылке, ничего не возвращает.
*/

function my_sort(&$array, $left, $right)
{

//Создаем копии пришедших переменных, с которыми будем манипулировать в дальнейшем.
    $l = $left;
    $r = $right;

    //Вычисляем 'центр', на который будем опираться. Берем значение ~центральной ячейки массива.
    $center = $array[(int)($left + $right) / 2];

    //Цикл, начинающий саму сортировку
    do {

//Ищем значения больше 'центра'
        while ($array[$r] > $center) {
            $r--;
        }

        //Ищем значения меньше 'центра'
        while ($array[$l] < $center) {
            $l++;
        }

        //После прох        ода циклов проверяем счетчики циклов
        if ($l <= $r) {

//И если условие true, то меняем ячейки друг с другом.
            list($array[$r], $array[$l]) = array($array[$l], $array[$r]);

            //И переводим счетчики на следующий элементы
            $l++;
            $r--;
        }

        //Повторяем цикл, если true
    } while ($l <= $r);

    if ($r > $left) {
        //Если условие true, совершаем рекурсию
        //Передаем массив, исх        одное начало и текущий конец
        my_sort($array, $left, $r);
    }

    if ($l < $right) {
        //Если условие true, совершаем рекурсию
        //Передаем массив, текущие начало и конец
        my_sort($array, $l, $right);
    }

    //Сортировка завершена
}
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
        echo("Connected to MySQL server.\n");
    }
    return $connection;
}
