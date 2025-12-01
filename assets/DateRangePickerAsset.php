<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class DateRangePickerAsset
 * @package app\assets
 */
class DateRangePickerAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'css/daterangepicker.css',
    ];

    /** @var string[] */
    public $js = [
        'js/moment-with-locales.min.js',
        'js/daterangepicker.min.js',
    ];
}
