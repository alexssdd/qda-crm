<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class DashboardAsset
 * @package app\assets
 */
class DashboardAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'css/animate.css',
        'css/dashboard/dashboard.css',
    ];

    /** @var string[] */
    public $js = [
        'js/dashboard/dashboard.js',
    ];

    /** @var string[] */
    public $depends = [
        'app\assets\AppAsset',
        'app\assets\DateRangePickerAsset',
        'yii\web\YiiAsset',
    ];
}
