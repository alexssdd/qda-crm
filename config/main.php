<?php

use yii\queue\db\Queue;
use yii\mutex\MysqlMutex;
use yii\helpers\ArrayHelper;
use app\core\bootstrap\ContainerBootstrap;

$params = ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
$db = require __DIR__ . '/db.php';
$sessionTimeout = 60 * 60 * 24 * 7;

return [
    'id' => 'app',
    'name' => 'CRM',
    'language' => 'ru',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'queue',
        ContainerBootstrap::class
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@storage' => dirname(__DIR__) . '/storage',
    ],
    // 'timeZone' => 'Asia/Aqtau',
    'modules' => [
        'mail' => ['class' => \app\modules\mail\Module::class],
        'auth' => ['class' => \app\modules\auth\Module::class],
    ],
    'timeZone' => 'Asia/Aqtau',
    'components' => [
        'request' => [
            'enableCsrfValidation' => true,
            'csrfCookie' => [
                'httpOnly' => true,
                'sameSite' => 'Lax',
                'secure' => YII_ENV_PROD,
            ],
        ],
        'response' => [
            'on beforeSend' => static function ($event): void {
                $headers = $event->sender->headers;
                $headers->set('Content-Security-Policy', "base-uri 'self'; form-action 'self'; frame-ancestors 'self'; object-src 'none'");
                $headers->set('Permissions-Policy', 'camera=(), microphone=()');
                $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
                $headers->set('X-Content-Type-Options', 'nosniff');
                $headers->set('X-Frame-Options', 'SAMEORIGIN');
                if (YII_ENV_PROD) {
                    $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
                }
            },
        ],
        'session' => [
            'timeout' => $sessionTimeout,
            'savePath' => '@runtime',
            'cookieParams' => [
                'lifetime' => $sessionTimeout,
                'httpOnly' => true,
                'sameSite' => 'Lax',
                'secure' => YII_ENV_PROD,
            ],
        ],
        'authManager' => [
            'class' => 'app\modules\auth\services\RbacService',
            'itemFile' => '@app/modules/auth/storage/rbac/items.php',
            'assignmentFile' => '@app/modules/auth/storage/rbac/assignments.php',
            'ruleFile' => '@app/modules/auth/storage/rbac/rules.php',
        ],
        'user' => [
            'identityClass' => 'app\modules\auth\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_identity',
                'httpOnly' => true,
                'sameSite' => 'Lax',
                'secure' => YII_ENV_PROD,
            ],
        ],
        'db' => $db['db'],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mutex' => [
            'class' => MysqlMutex::class,
            'db' => 'db',
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
