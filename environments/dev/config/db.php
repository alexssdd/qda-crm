<?php
return [
    'db' => [
        'class' => 'yii\db\Connection',
        'charset' => 'utf8mb4',
        'dsn' => 'mysql:host=global-db;dbname=global_db',
        'username' => 'app_db',
        'password' => 'pwd',
        'tablePrefix' => 'ow_'
    ],
];