<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Care asset
 */
class CareAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'css/care.css',
        'css/timeto.css'
    ];

    /** @var string[] */
    public $js = [
        'js/care.js',
        'js/time-to.min.js'
    ];

    /** @var string[] */
    public $depends = [
        'app\assets\AppAsset',
    ];
}
