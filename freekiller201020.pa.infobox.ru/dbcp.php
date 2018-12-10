<?php

$file  = file_get_contents(realpath(dirname(__FILE__))."/../config.json");
$config = json_decode($file, true);
$connection=Connect($config);

$file2  = file_get_contents(realpath(dirname(__FILE__))."/../config_local.json");
$config2 = json_decode($file2, true);
$connection2=Connect($config2);

$limit=100;

// $query="Select count(*) from {$config2["database"]}.Players";

$query="Select count(*) from {$config["base_database"]}.Players";
echo PHP_EOL.$query.PHP_EOL.PHP_EOL;
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
  $count=$row["count(*)"];
}

$t0=microtime(true)*10000;
$cnt=0;

while($cnt<$count+$limit){
  $t=(microtime(true)*10000-$t0)/$cnt;
  progressBar($cnt, $count, $t, $t0);
  $query="Select * from {$config["base_database"]}.Players limit $limit offset $cnt";
  // echo $query;
  $cnt+=$limit;
  $result = $connection->query($query);
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // print_r($row);
        // $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";

        $query2 = "INSERT INTO {$config2["database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"{$row["timemark"]}\",{$row["id"]},'{$row["nick"]}',{$row["frags"]},{$row["deaths"]},{$row["level"]},{$row["clan_id"]});\n";
        // $query2="Insert into Players $row";
        $result2 = $connection2->query($query2);
      }
  }
}


$query="Select count(*) from {$config["base_database"]}.Players_fast";
echo PHP_EOL.$query.PHP_EOL.PHP_EOL;
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
  $count=$row["count(*)"];
}

$t0=microtime(true)*10000;
$cnt=0;
while($cnt<$count+$limit){
  $t=(microtime(true)*10000-$t0)/$cnt;
  progressBar($cnt, $count, $t, $t0);
  $cnt+=$limit;
  $query="Select * from {$config["base_database"]}.Players_fast limit $limit offset $cnt";
  $result = $connection->query($query);
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // print_r($row);
        // $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";

        $query2 = "INSERT INTO {$config2["database"]}.Players_fast (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"{$row["timemark"]}\",{$row["id"]},'{$row["nick"]}',{$row["frags"]},{$row["deaths"]},{$row["level"]},{$row["clan_id"]});\n";
        // $query2="Insert into Players $row";
        $result2 = $connection2->query($query2);
      }
  }
}

$query="Select count(*) from {$config["base_database"]}.Attacks";
echo PHP_EOL.$query.PHP_EOL.PHP_EOL;
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
  $count=$row["count(*)"];
}

$t0=microtime(true)*10000;
$cnt=0;
while($cnt<$count+$limit){
  $t=(microtime(true)*10000-$t0)/$cnt;
  progressBar($cnt, $count, $t, $t0);
  $cnt+=$limit;
  $query="Select * from {$config["base_database"]}.Attacks limit $limit offset $cnt";
  $result = $connection->query($query);
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // print_r($row);
        // $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";
        // $query = "INSERT INTO {$config["base_database"]}.Attacks (fromm,too,attacker,defender,declared,resolved) VALUES (\"{$attack["from"]}\",\"{$attack["to"]}\",$attacker,$defender,\"{$attack["declared"]}\",\"{$attack["resolved"]}\");\n";

        $query2 = "INSERT INTO {$config2["database"]}.Attacks (fromm,too,attacker,defender,declared,resolved) VALUES (\"{$row["from"]}\",\"{$row["to"]}\",{$row["attacker"]},{$row["defender"]},\"{$row["declared"]}\",\"{$row["resolved"]}\");\n";
        // $query2="Insert into Players $row";
        $result2 = $connection2->query($query2);
      }
  }
}


$query="Select count(*) from {$config["base_database"]}.Attacks_fast";
echo PHP_EOL.$query.PHP_EOL.PHP_EOL;
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
  $count=$row["count(*)"];
}

$t0=microtime(true)*10000;
$cnt=0;
while($cnt<$count+$limit){
  $t=(microtime(true)*10000-$t0)/$cnt;
  progressBar($cnt, $count, $t, $t0);
  $cnt+=$limit;
  $query="Select * from {$config["base_database"]}.Attacks_fast limit $limit offset $cnt";
  $result = $connection->query($query);
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // print_r($row);
        // $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";
        // $query = "INSERT INTO {$config["base_database"]}.Clans (timemark,id,title,points) VALUES (\"$date\",{$clan["id"]},'$title',$points);\n";
        // $query = "INSERT INTO {$config["base_database"]}.Attacks_fast (fromm,too,attacker,defender,declared,resolved,ended) VALUES (\"{$attack["from"]}\",\"{$attack["to"]}\",$attacker,$defender,\"{$date1}\",\"{$date2}\",NULL);\n";

        $query2 = "INSERT INTO {$config2["database"]}.Attacks_fast (fromm,too,attacker,defender,declared,resolved,ended) VALUES (\"{$row["from"]}\",\"{$row["to"]}\",{$row["attacker"]},{$row["defender"]},\"{$row["declared"]}\",\"{$row["resolved"]}\",\"{$row["ended"]}\");\n";
        // $query2="Insert into Players $row";
        $result2 = $connection2->query($query2);
      }
  }
}

$query="Select count(*) from {$config["base_database"]}.Clans";
echo PHP_EOL.$query.PHP_EOL.PHP_EOL;
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
  $count=$row["count(*)"];
}
$t0=microtime(true)*10000;
$cnt=0;
while($cnt<$count+$limit){
  $t=(microtime(true)*10000-$t0)/$cnt;
  progressBar($cnt, $count, $t, $t0);
  $cnt+=$limit;
  $query="Select * from {$config["base_database"]}.Clans limit $limit offset $cnt";
  $result = $connection->query($query);
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // print_r($row);
        // $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";
        // $query = "INSERT INTO {$config["base_database"]}.Clans (timemark,id,title,points) VALUES (\"$date\",{$clan["id"]},'$title',$points);\n";

        $query2 = "INSERT INTO {$config2["database"]}.Clans (timemark,id,title,points) VALUES (\"{$row["timemark"]}\",{$row["id"]},'{$row["title"]}',{$row["points"]});\n";
        // $query2="Insert into Players $row";
        $result2 = $connection2->query($query2);
      }
  }
}


$query="Select count(*) from {$config["base_database"]}.Clans_fast";
echo PHP_EOL.$query.PHP_EOL.PHP_EOL;
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
  $count=$row["count(*)"];
}

$t0=microtime(true)*10000;
$cnt=0;
while($cnt<$count+$limit){
  $t=(microtime(true)*10000-$t0)/$cnt;
  progressBar($cnt, $count, $t, $t0);
  $cnt+=$limit;
  $query="Select * from {$config["base_database"]}.Clans_fast limit $limit offset $cnt";
  $result = $connection->query($query);
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // print_r($row);
        // $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";
        // $query = "INSERT INTO {$config["base_database"]}.Clans (timemark,id,title,points) VALUES (\"$date\",{$clan["id"]},'$title',$points);\n";

        $query2 = "INSERT INTO {$config2["database"]}.Clans_fast (timemark,id,title,points) VALUES (\"{$row["timemark"]}\",{$row["id"]},'{$row["title"]}',{$row["points"]});\n";
        // $query2="Insert into Players $row";
        $result2 = $connection2->query($query2);
      }
  }
}


$query="Select count(*) from {$config["base_database"]}.Cities";
echo PHP_EOL.$query.PHP_EOL.PHP_EOL;
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
  $count=$row["count(*)"];
}

$t0=microtime(true)*10000;
$cnt=0;
while($cnt<$count+$limit){
  $t=(microtime(true)*10000-$t0)/$cnt;
  progressBar($cnt, $count, $t, $t0);
  $cnt+=$limit;
  $query="Select * from {$config["base_database"]}.Cities limit $limit offset $cnt";
  $result = $connection->query($query);
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // print_r($row);
        // $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";
        // $query = "INSERT INTO {$config["base_database"]}.Cities (timemark,id,name,clan) VALUES (\"$date\",{$city["id"]},\"{$city["name"]}\",NULL);\n";
        if ($city["clan"]==null) {
          $query2 = "INSERT INTO {$config2["database"]}.Cities (timemark,id,name,clan) VALUES (\"{$row["timemark"]}\",{$row["id"]},\"{$row["name"]}\",NULL);\n";
        }
        else{
          $query2 = "INSERT INTO {$config2["database"]}.Cities (timemark,id,name,clan) VALUES (\"{$row["timemark"]}\",{$row["id"]},\"{$row["name"]}\",{$row["clan"]});\n";
        }
        // echo $query2.PHP_EOL;
        // $query2="Insert into Players $row";
        $result2 = $connection2->query($query2);
      }
  }
}


$query="Select count(*) from {$config["base_database"]}.Cities_fast";
echo PHP_EOL.$query.PHP_EOL.PHP_EOL;
$result = $connection->query($query);
while ($row = $result->fetch_assoc()) {
  $count=$row["count(*)"];
}

$t0=microtime(true)*10000;
$cnt=0;
while($cnt<$count+$limit){
  $t=(microtime(true)*10000-$t0)/$cnt;
  progressBar($cnt, $count, $t, $t0);
  $cnt+=$limit;
  $query="Select * from {$config["base_database"]}.Cities_fast limit $limit offset $cnt";
  $result = $connection->query($query);
  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // print_r($row);
        // $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";
        // $query = "INSERT INTO {$config["base_database"]}.Cities (timemark,id,name,clan) VALUES (\"$date\",{$city["id"]},\"{$city["name"]}\",NULL);\n";
        if ($city["clan"]==null) {
          $query2 = "INSERT INTO {$config2["database"]}.Cities_fast (timemark,id,name,clan) VALUES (\"{$row["timemark"]}\",{$row["id"]},\"{$row["name"]}\",NULL);\n";
        }
        else{
          $query2 = "INSERT INTO {$config2["database"]}.Cities_fast (timemark,id,name,clan) VALUES (\"{$row["timemark"]}\",{$row["id"]},\"{$row["name"]}\",{$row["clan"]});\n";
        }
        // echo $query2.PHP_EOL;
        // $query2="Insert into Players $row";
        $result2 = $connection2->query($query2);
      }
  }
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
