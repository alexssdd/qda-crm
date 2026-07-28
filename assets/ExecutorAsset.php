<?php

namespace app\assets;

use yii\web\AssetBundle;

class ExecutorAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/executor.css',
    ];

    public $js = [
        'js/executor.js',
    ];

    public $depends = [
        AppAsset::class,
        DateRangePickerAsset::class,
    ];
}
