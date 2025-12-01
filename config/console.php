<?php

use yii\helpers\ArrayHelper;
use yii\queue\db\Queue;
use yii\mutex\MysqlMutex;

$params = ArrayHelper::merge(
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

$db = require __DIR__ . '/db.php';

return [
    'id' => 'app-console',
    'language' => 'ru',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'queue',
    ],
    'aliases' => [
        '@storage' => dirname(__DIR__) . '/storage',
    ],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'halyk' => ['class' => \app\modules\halyk\Module::class],
        'stock' => ['class' => \app\modules\stock\Module::class],
        'zvonobot' => ['class' => \app\modules\zvonobot\Module::class],
        'telegram' => ['class' => \app\modules\telegram\Module::class],
        'yandex' => ['class' => \app\modules\yandex\Module::class],
        'kaspi' => ['class' => \app\modules\kaspi\Module::class],
        'jusan' => ['class' => \app\modules\jusan\Module::class],
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
    'components' => [
        'authManager' => [
            'class' => 'app\core\rbac\AuthManager',
            'itemFile' => '@app/core/rbac/items/items.php',
            'assignmentFile' => '@app/core/rbac/items/assignments.php',
            'ruleFile' => '@app/core/rbac/items/rules.php',
        ],
        'db' => $db['db'],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile' => '@app/runtime/logs/console-error.log'
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'logFile' => '@app/runtime/logs/console-warning.log'
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
    ],
    'params' => $params,
];