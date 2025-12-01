<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Empty asset
 */
class EmptyAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'css/icons.css',
        'css/empty.css',
    ];

    /** @var string[] */
    public $depends = [
        'yii\web\YiiAsset'
    ];
}
