<?php

use yii\queue\db\Queue;
use yii\mutex\MysqlMutex;
use yii\helpers\ArrayHelper;

$params = ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
$db = require __DIR__ . '/db.php';

return [
    'id' => 'app',
    'name' => 'CRM',
    'language' => 'ru',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'queue',
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@storage' => dirname(__DIR__) . '/storage',
    ],
    // 'timeZone' => 'Asia/Aqtau',
    'modules' => [
        'halyk' => ['class' => \app\modules\halyk\Module::class],
        'stock' => ['class' => \app\modules\stock\Module::class],
        'jivosite' => ['class' => \app\modules\jivosite\Module::class],
        'telegram' => ['class' => \app\modules\telegram\Module::class],
        'yandex' => ['class' => \app\modules\yandex\Module::class],
        'wb' => ['class' => \app\modules\wb\Module::class],
        'forte' => ['class' => \app\modules\forte\Module::class],
        'scrape' => ['class' => \app\modules\scrape\Module::class],
        'google' => ['class' => \app\modules\google\Module::class],
        'sl' => ['class' => \app\modules\sl\Module::class],
        'mail' => ['class' => \app\modules\mail\Module::class],
        'devino' => ['class' => \app\modules\devino\Module::class],
        'edna' => ['class' => \app\modules\edna\Module::class],
        'infobip' => ['class' => \app\modules\infobip\Module::class],
        'ax' => ['class' => \app\modules\ax\Module::class],
    ],
    'timeZone' => 'Asia/Aqtau',
    'components' => [
        'authManager' => [
            'class' => 'app\core\rbac\AuthManager',
            'itemFile' => '@app/core/rbac/items/items.php',
            'assignmentFile' => '@app/core/rbac/items/assignments.php',
            'ruleFile' => '@app/core/rbac/items/rules.php',
        ],
        'user' => [
            'identityClass' => 'app\entities\User',
            'enableAutoLogin' => true,
        ],
        'db' => $db['db'],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                ],
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'forceTranslation' => true,
                ],
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile' => '@app/runtime/logs/app-error.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'logFile' => '@app/runtime/logs/app-warning.log'
                ],
            ],
        ],
        'queue' => [
            'class' => Queue::class,
            'db' => 'db', // Компонент подключения к БД или его конфиг
            'channel' => 'default', // Выбранный для очереди канал
            'mutex' => MysqlMutex::class, // Мьютекс для синхронизации запросов
            'ttr' => 2 * 60, // Максимальное время выполнения задания
            'attempts' => 3, // Максимальное кол-во попыток
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'normalizer' => [
                'class' => \yii\web\UrlNormalizer::class
            ],
            'rules' => require 'urls.php',
        ],
        'assetManager' => [
            'linkAssets' => true,
            'appendTimestamp' => true
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'timeZone' => 'Asia/Aqtau',
            'dateFormat' => 'php:d.m.Y',
            'datetimeFormat' => 'php:d.m.y H:i',
            'timeFormat' => 'php:H:i:s',
            'thousandSeparator' => ' ',
            'decimalSeparator' => '.'
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
