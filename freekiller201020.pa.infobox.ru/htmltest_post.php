<?php
session_start();
if (($_SESSION['u']!=null)&&($_SESSION['p']!=null)) {
    $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'];
    $url .= ":".$_SERVER['SERVER_PORT'];
    $url .= $_SERVER['REQUEST_URI'];

    $url =dirname($url)."/fast_data3.php";
    header("Location: $url");
    die();
}

// $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
// $url .= $_SERVER['SERVER_NAME'];
//                         $url .= ":".$_SERVER['SERVER_PORT'];
// $url .= $_SERVER['REQUEST_URI'];
//
// $url =dirname($url)."/test123.php";
// $file  = file_get_contents($url);


$file  = file_get_contents("http://www.clanberserk.ru/script1.php?username={$_REQUEST['username']}&password={$_REQUEST['password']}");

// $file  = file_get_contents("http://www.clanberserk.ru/script1.php?username=freekiller201020&password=3XYT7WXW72PEQ");
// echo "http://www.clanberserk.ru/script1.php?username={$_REQUEST['username']}&password={$_REQUEST['password']}";
if (empty($file)) {
    exit();
}
$json = json_decode($file, true);
// print_r($json);


if (!$json["err"]) {
    if (($json["u"])&&(($json["p"]))) {
        $_SESSION['u']=$json["u"];
        $_SESSION['p']=$json["p"];
    }
    $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'];
    $url .= ":".$_SERVER['SERVER_PORT'];
    $url .= $_SERVER['REQUEST_URI'];

    $url =dirname($url)."/fast_data3.php";
    header("Location: $url");
    die();
} else {
    echo "error";
}
