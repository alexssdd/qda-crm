<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class ChartAsset
 * @package app\assets
 */
class ChartAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap',
        'css/icons.css',
        'css/animate.css',
        'css/chart/apexcharts.css',
        'css/chart/chart.css',
    ];

    /** @var string[] */
    public $js = [
        'js/chart/apexcharts.min.js',
        'js/chart/chart.js',
        'js/chart/order.js',
        'js/chart/sale.js',
        'js/chart/chat.js',
        'js/chart/product.js'
    ];

    /** @var string[] */
    public $depends = [
        'app\assets\DateRangePickerAsset',
        'yii\web\YiiAsset',
    ];
}
