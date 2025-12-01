<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class CartAsset
 * @package app\assets
 */
class YandexMapAsset extends AssetBundle
{
    /** @var string[] */
    public $js = [
        'https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=67c510cd-7935-4bcb-bc5b-b8a756ca31da&suggest_apikey=09258749-7434-4d57-bd7a-86e2dcae3ee7'
    ];

    /** @var string[] */
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
