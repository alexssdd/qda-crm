<?php
return [
    'db' => [
        'class' => 'yii\db\Connection',
        'charset' => 'utf8mb4',
        // CRM и Global используют одну БД на этой же ноде.
        // localhost подключает MariaDB через unix socket.
        'dsn' => 'mysql:host=localhost;dbname=global_db',
        'username' => 'global_app',
        // `php init` копирует файл без подстановки переменных.
        // После init заменить placeholder в config/db.php реальным паролем.
        'password' => '<global-app-password>',
        'tablePrefix' => 'ow_',
    ],
];
