<?php
// Эта страница авторизации на форуме
define('IN_PHPBB', true);

define('PHPBB_ROOT_PATH', './forum/');
$dt=array();
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);

$username = request_var('username', '', true);
$password = request_var('password', '', true);

if (!$username || !$password) {
    // echo "Пожалуйста введите имя и пароль<br />";
    $dt["err"]=1;
} else {
    // Подготовка username к поиску в базе данных форму
    $username = utf8_clean_string($username);

    //Ищем username
    $sql = 'SELECT user_password
		FROM ' . USERS_TABLE . '
		WHERE username_clean = \'' . $db->sql_escape($username) . '\'';
    $result = $db->sql_query($sql);

    if (!$find_row = $db->sql_fetchfield('user_password')) {
        // echo "Такое имя не найдено в базе данных форума<br />";
        $dt["err"]=2;
    } else {
        // echo "Такое имя есть в базе данных форума. <br/>";
        $dt["u"]=$username;

        // Проверяем пароль
        $password_hash = $find_row;

        $check = phpbb_check_hash($password, $password_hash);

        if ($check == false) {
            // echo "Проверку пароль не прошел!";
            $dt["err"]=3;
        } else {
            // echo "Проверку пароль прошел!";
            $dt["p"]=$password;
        }
    }
    $db->sql_freeresult($result);
    echo json_encode($dt);
}
