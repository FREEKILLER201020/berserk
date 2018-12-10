<?php

$file  = file_get_contents(realpath(dirname(__FILE__))."/../config_local.json");
$config = json_decode($file, true);
$connection=Connect($config);

$filename="00068456.jpg";
$imgData = file_get_contents($filename);
$size = getimagesize($filename);

// mysqli
// $link = mysqli_connect("localhost", $username, $password,$dbname);
$sql = sprintf("INSERT INTO testblob
    (image_type, image, image_size, image_name)
    VALUES
    ('%s', '%s', '%d', '%s')",

    $connection->real_escape_string($size['mime']),
    $connection->real_escape_string($imgData),
    $size[3],
    $connection->real_escape_string($_FILES['userfile']['name'])
    );
file_put_contents("sql.txt", $sql);
    $result=$connection->query($sql);
    if (!$result) {
        echo("Error during deleting base table".$connection->connect_errno.$connection->connect_error);
    }
// mysql_query($sql);


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
