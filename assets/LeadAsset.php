<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Lead asset
 */
class LeadAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'css/lead.css',
        'css/timeto.css'
    ];

    /** @var string[] */
    public $js = [
        'js/lead.js',
        'js/time-to.min.js'
    ];

    /** @var string[] */
    public $depends = [
        'app\assets\AppAsset'
    ];
}
