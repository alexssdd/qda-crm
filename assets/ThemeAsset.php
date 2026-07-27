<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Theme overrides that must be loaded after all page-specific styles.
 */
class ThemeAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'css/theme.css',
    ];

    /** @var string[] */
    public $depends = [
        AppAsset::class,
    ];
}
