<?php

$db = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2_oauth2_server_test',
    'username' => 'root',
    'password' => '7h8j9k',
    'charset' => 'utf8',
];

if (file_exists(__DIR__.'/db.local.php')) {
    $db = array_merge($db, require(__DIR__.'/db.local.php'));
}

return $db;
