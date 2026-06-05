<?php
// DB接続ファイル

//
define('DB_USERNAME', 'teamL');
define('DB_PASSWORD', 'teaml');
define('DSN', 'mysql:host=localhost;dbname=systemdev;charset=utf8mb4');

// DB connect လုပ်တဲ့ function
function db_connect(): PDO
{
    // new PDO(...) PHP နဲ့ MySQL ချိတ်တဲ့ object
    $dbh = new PDO(
        DSN,
        DB_USERNAME,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    // Connection ကိုပြန်ပေးတာ
    return $dbh;
}

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
