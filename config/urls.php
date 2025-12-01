<?php
return [
    '' => 'site/index',
    'login' => 'site/login',
    'logout' => 'site/logout',

    // Market
    'GET market/callback' => 'market/callback/index',

    // Wms
    'GET wms/orders' => 'wms/order/index',
    'GET wms/orders/<number:\d+>' => 'wms/order/view',
    'POST wms/orders/<number:\d+>/assembled' => 'wms/order/assembled',
    'POST wms/orders/<number:\d+>/cancel' => 'wms/order/cancel',
    'POST wms/orders/<number:\d+>/issued' => 'wms/order/issued',

    // Register
    'GET wms/registers' => 'wms/register/index',
    'GET wms/registers/<number:\d+>' => 'wms/register/view',
    'POST wms/registers/<number:\d+>/assembled' => 'wms/register/assembled',
    'POST wms/registers/<number:\d+>/issued' => 'wms/register/issued',

    // Tms
    'POST tms/orders/<number:\d+>/courier' => 'tms/order/courier',
    'POST tms/orders/<number:\d+>/delivered' => 'tms/order/delivered',

    // Payment
    'GET payment/<token:\w+>' => 'payment/index',
    'GET payment/success/<token:\w+>' => 'payment/success',
    'GET payment/failure/<token:\w+>' => 'payment/failure',
    'GET payment/<code:\w+>/<token:\w+>' => 'payment/view',
    'GET payment/<code:\w+>/<token:\w+>/<type:\d+>' => 'payment/widget',

    // Halyk
    'POST halyk/payment/success' => 'halyk/payment/success',
    'POST halyk/payment/failure' => 'halyk/payment/failure',

    // jivosite
    'POST jivosite/webhook' => 'jivosite/webhook/index',

    // Telegram
    'POST telegram/webhook' => 'telegram/webhook/index',

    // Common
    '<_c:[\w-]+>' => '<_c>/index',
    '<_c:[\w-]+>/<_a:[\w-]+>' => '<_c>/<_a>'
];