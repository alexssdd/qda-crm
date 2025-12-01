<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class AnimateAsset
 * @package app\assets
 */
class AnimateAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@bower/animate.css';

    /** @var string[] */
    public $css = [
        'animate.min.css',
    ];
}
