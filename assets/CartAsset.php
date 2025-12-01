<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Cart asset
 */
class CartAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'css/cart.css'
    ];

    /** @var string[] */
    public $js = [
        'js/cart.js'
    ];

    /** @var string[] */
    public $depends = [
        'app\assets\AppAsset',
        'app\assets\YandexMapAsset',
    ];
}
