<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Appeal asset
 */
class AppealAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'css/appeal.css'
    ];

    /** @var string[] */
    public $js = [
        'js/appeal.js'
    ];

    /** @var string[] */
    public $depends = [
        'app\assets\AppAsset',
    ];
}
