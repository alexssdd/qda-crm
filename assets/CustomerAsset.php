<?php

namespace app\assets;

use yii\web\AssetBundle;

class CustomerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/customer.js',
    ];

    public $depends = [
        AppAsset::class,
        DateRangePickerAsset::class,
    ];
}
