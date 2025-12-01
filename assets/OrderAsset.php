<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Order asset
 */
class OrderAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'css/order.css',
        'css/timeto.css'
    ];

    /** @var string[] */
    public $js = [
        'js/order.js',
        'js/time-to.min.js'
    ];

    /** @var string[] */
    public $depends = [
        'app\assets\AppAsset',
        'app\assets\YandexMapAsset',
    ];
}
