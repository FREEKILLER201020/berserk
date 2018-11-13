<?php
require("classes.php");
require("data.php");
$file  = file_get_contents(realpath(dirname(__FILE__))."/../config.json");
$config = json_decode($file, true);
$connection=Connect($config);

$del=0;
$long=0;
$total=0;
$lim=1;
$lim_set=0;
$notif=0;
$sync=0;
$scan=0;
$path_scan="";
  for ($i=0;$i<count($argv);$i++) {
      if ($argv[$i] == "-d") {
          $del=1;
      }
      if ($argv[$i] == "-l") {
          $long=1;
      }
      if ($argv[$i] == "-lim") {
          $lim=$argv[$i+1];
          $lim_set=1;
      }
      if ($argv[$i] == "-path") {
          $path_scan=$argv[$i+1];
      }
      if ($argv[$i] == "-n") {
          $notif=1;
      }
      if ($argv[$i] == "-sync") {
          $sync=1;
      }
      if ($argv[$i] == "-scan") {
          $scan=1;
      }
      if ($argv[$i] == "-help") {
          echo "
          -d      Delete all data (strat over)
          -n      Sent notification when job is done
          -sync   Sync DATA folder with DATA folder in scaner subdirection
          -scan   Run data scaner after emulation
          -lim    Set fights limt
          -path   Path to the folder with era data
          -help   Show 'help' note ;)";
          exit();
      }
  }


CheckDatabase($connection, $config);
CheckTables($connection, $config);
Scan($connection, $config);

// END!!!!!

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

function Check_server($connection, $config, $del) // Функция проверки состояния баз в БД и самой БД, при необходимости создаем их (если БД "новая")
{
    if ($del ==1) {
        $query = "DROP DATABASE {$config["base_database"]};\n";
        $result = $connection->query($query);
        if (!$result) {
            echo("Error during deleting base table".$connection->connect_errno.$connection->connect_error);
        }
    }
    $query = "CREATE DATABASE IF NOT EXISTS {$config["base_database"]};\n";
    $result = $connection->query($query);
    if (!$result) {
        die("Error during creating base tablee".$connection->connect_errno.$connection->connect_error);
    }
    $query = "CREATE TABLE IF NOT EXISTS {$config["base_database"]}.eras (num INT,start DATETIME,ends DATETIME,base NVARCHAR(50),PRIMARY KEY (num))";
    $result = $connection->query($query);
    if (!$result) {
        die("Error during creating table".$connection->connect_errno.$connection->connect_error);
    }
}

function GetCurrentEra($connection, $config)
{
    $query = "SELECT * FROM {$config["base_database"]}.eras ORDER BY start DESC LIMIT 1;\n";
    $result = $connection->query($query);
    while ($row = $result->fetch_assoc()) {
        foreach ($row as $key=> $data) {
            if ($key=="num") {
                return $data;
            }
        }
    }
}

function CheckDatabase($connection, $config)
{
    $query = "SHOW DATABASES LIKE '{$config["base_database"]}';\n";
    $result = $connection->query($query);
    if ($result->num_rows > 0) {
        echo "Database exist";
    } else {
        $query = "CREATE DATABASE IF NOT EXISTS {$config["base_database"]};\n";
        echo $query;
        $result = $connection->query($query);
        if (!$result) {
            die("Error during creating era table".$connection->connect_errno.$connection->connect_error);
        }
    }
}

function GetActiveEra($connection, $config)
{
    $query = "SELECT * FROM {$config["base_database"]}.eras WHERE ends IS NULL ORDER BY start DESC LIMIT 1;\n";
    $result = $connection->query($query);
    print_r($result);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key=> $data) {
                if ($key=="num") {
                    return $data;
                }
            }
        }
    } else {
        return -1;
    }
}

function GetLastEra($connection, $config)
{
    $query = "SELECT * FROM {$config["base_database"]}.eras WHERE ends IS NOT NULL ORDER BY start DESC LIMIT 1;\n";
    echo $query;
    $result = $connection->query($query);
    // print_r($result);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key=> $data) {
                echo "$key => $data";
                if ($key=="num") {
                    return $data;
                }
            }
        }
    } else {
        return -1;
    }
}

function GetLastDay($connection, $config, $number)
{
    $query = "SELECT * FROM {$config["base_database"]}$number.Dates ORDER BY timemark DESC LIMIT 1;\n";
    echo $query;
    $result = $connection->query($query);
    // print_r($result);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key=> $data) {
                echo "$key => $data";
                if ($key=="num") {
                    return $data;
                }
            }
        }
    } else {
        return 0;
    }
}

function AddEra($connection, $config, $number)
{
    $date=date("Y-m-d");
    $query = "INSERT INTO {$config["base_database"]}.eras (num,start,ends,base) VALUES ($number,\"$date\",NULL,\"era$number\");";
    echo $query."\n";
    $result = $connection->query($query);
    if (!$result) {
        die("Error during adding era".$connection->connect_errno.$connection->connect_error);
    }
}

function EndEra($connection, $config, $number)
{
    $date=date("Y-m-d");
    $query = "UPDATE {$config["base_database"]}.eras SET ends=\"$date\" WHERE num=$number;";
    echo $query."\n";
    $result = $connection->query($query);
    if (!$result) {
        die("Error during adding era".$connection->connect_errno.$connection->connect_error);
    }
}

function CheckTables($connection, $config)
{
    $query = "CREATE TABLE IF NOT EXISTS {$config["base_database"]}.Clans (timemark DATE,id INT, title NVARCHAR(100),points INT);\n";
    echo $query;
    $result = $connection->query($query);
    if (!$result) {
        die("Error during creating palyers table".$connection->connect_errno.$connection->connect_error);
    }
    $query = "CREATE TABLE IF NOT EXISTS {$config["base_database"]}.Cities_fast (timemark DATE,id INT, name NVARCHAR(100),clan INT);\n";
    echo $query;
    $result = $connection->query($query);
    if (!$result) {
        die("Error during creating palyers table".$connection->connect_errno.$connection->connect_error);
    }
    $query = "CREATE TABLE IF NOT EXISTS {$config["base_database"]}.Players (timemark DATE,id INT,nick NVARCHAR(100), frags INT, deaths INT,level INT, clan_id INT);\n";
    echo $query;
    $result = $connection->query($query);
    if (!$result) {
        die("Error during creating clans table".$connection->connect_errno.$connection->connect_error);
    }
    $query = "CREATE TABLE IF NOT EXISTS {$config["base_database"]}.Attacks (fromm NVARCHAR(100),too NVARCHAR(100), attacker INT, defender INT, declared DATETIME, resolved DATETIME);\n";
    echo $query;
    $result = $connection->query($query);
    if (!$result) {
        die("Error during creating attacks table".$connection->connect_errno.$connection->connect_error);
    }
}

function Scan($connection, $config)
{
    $date=date("Y-m-d", time()-24*60*60*0);
    $query="SELECT * FROM {$config["base_database"]}.Players WHERE timemark=\"$date\"";
    echo $query;
    $result = $connection->query($query);
    if ($result->num_rows <= 0) {
        $clans=GetClans();
        print_r($clans);
        foreach ($clans as $clan) {
            if ($clan["points"]==0) {
                $points="null";
            } else {
                $points=$clan["points"];
            }
            $title=Restring($clan["title"]);
            // $query="SELECT * FROM {$config["base_database"]}.Clans WHERE id={$clan["id"]} and timemark=\"$date\"";
            // echo $query;
            // $result = $connection->query($query);
            // print_r($result);
            // if ($result->num_rows <= 0) {
            $query = "INSERT INTO {$config["base_database"]}.Clans (timemark,id,title,points) VALUES (\"$date\",{$clan["id"]},'$title',$points);\n";
            echo $query;
            $result = $connection->query($query);
            if (!$result) {
                die("Error during filling clans table".$connection->connect_errno.$connection->connect_error);
            }
            // } else {
            //     $query = "UPDATE {$config["base_database"]}.Clans SET title='$title' ,points=$points WHERE id={$clan["id"]}\n";
            //     echo $query;
            //     $result = $connection->query($query);
            //     if (!$result) {
            //         die("Error during filling clans table".$connection->connect_errno.$connection->connect_error);
            //     }
            // }
            $players=GetClanData($clan["id"]);
            print_r($players);
            foreach ($players as $key=> $data) {
                // echo "$key => $data";
                if ($key=="players") {
                    foreach ($data as $player) {
                        print_r($player);
                        $nick=Restring($player["nick"]);
                        $query = "INSERT INTO {$config["base_database"]}.Players (timemark,id,nick,frags,deaths,level,clan_id) VALUES (\"$date\",{$player["id"]},'$nick',{$player["frags"]},{$player["deaths"]},{$player["level"]},{$clan["id"]});\n";
                        echo $query;
                        $result = $connection->query($query);
                        if (!$result) {
                            die("Error during filling players table".$connection->connect_errno.$connection->connect_error);
                        }
                    }
                }
            }
        }
        $attacks=GetFights();
        print_r($attacks);
        foreach ($attacks as $attack) {
            $query="SELECT * FROM {$config["base_database"]}.Attacks WHERE attacker=".GetClanID($connection, $config, $attack["attacker"], $date)." and defender=".GetClanID($connection, $config, $attack["defender"], $date)." and declared=\"{$attack["declared"]}\" and resolved=\"{$attack["resolved"]}\"";
            echo $query;
            $result = $connection->query($query);
            if ($result->num_rows <= 0) {
                $attacker=GetClanID($connection, $config, $attack["attacker"], $date);
                $defender=GetClanID($connection, $config, $attack["defender"], $date);
                $query = "INSERT INTO {$config["base_database"]}.Attacks (fromm,too,attacker,defender,declared,resolved) VALUES (\"{$attack["from"]}\",\"{$attack["to"]}\",$attacker,$defender,\"{$attack["declared"]}\",\"{$attack["resolved"]}\");\n";
                echo $query;
                $result = $connection->query($query);
                if (!$result) {
                    die("Error during filling cities table".$connection->connect_errno.$connection->connect_error);
                }
            }
        }
        $cities=GetCities();
        print_r($cities);
        foreach ($cities as $city) {
            // $query="SELECT * FROM {$config["base_database"]}.Cities_fast WHERE id=".GetClanID($connection, $config, $attack["attacker"], $date)." and defender=".GetClanID($connection, $config, $attack["defender"], $date)." and declared=\"{$attack["declared"]}\" and resolved=\"{$attack["resolved"]}\"";
            // echo $query;
            // $result = $connection->query($query);
            // if ($result->num_rows <= 0) {
            //     $attacker=GetClanID($connection, $config, $attack["attacker"], $date);
            //     $defender=GetClanID($connection, $config, $attack["defender"], $date);
            print_r($city);
            if ($city["clan"]==null) {
                echo "here";
                $query = "INSERT INTO {$config["base_database"]}.Cities_fast (timemark,id,name,clan) VALUES (\"$date\",{$city["id"]},\"{$city["name"]}\",NULL);\n";
            } else {
                $query = "INSERT INTO {$config["base_database"]}.Cities_fast (timemark,id,name,clan) VALUES (\"$date\",{$city["id"]},\"{$city["name"]}\",{$city["clan"]});\n";
            }
            echo $query;
            $result = $connection->query($query);
            if (!$result) {
                die("Error during filling cities table".$connection->connect_errno.$connection->connect_error);
            }
            // }
        }
    }
}

function GetClanID($connection, $config, $name, $date)
{
    $query = "SELECT * FROM {$config["base_database"]}.Clans WHERE title='$name' and timemark=\"$date\";\n";
    echo $query;
    $result = $connection->query($query);
    // print_r($result);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key=> $data) {
                echo "$key => $data";
                if ($key=="id") {
                    return $data;
                }
            }
        }
    } else {
        return -1;
    }
}

function GetCityID($connection, $config, $number, $name, $day)
{
    $query = "SELECT * FROM {$config["base_database"]}.Cities WHERE name='$name';\n";
    echo $query;
    $result = $connection->query($query);
    // print_r($result);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key=> $data) {
                echo "$key => $data";
                if ($key=="id") {
                    return $data;
                }
            }
        }
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
