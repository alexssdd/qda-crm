<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class ApexChartsAsset
 * @package app\assets
 */
class ApexChartsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'css/chart/apexcharts.css',
    ];

    /** @var string[] */
    public $js = [
        'js/chart/apexcharts.min.js',
    ];
}
