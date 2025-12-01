<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    /** @var string */
    public $basePath = '@webroot';

    /** @var string */
    public $baseUrl = '@web';

    /** @var string[] */
    public $css = [
        'css/icons.css',
        'css/nprogress.css',
        'css/app.css',
    ];

    /** @var string[] */
    public $js = [
        'js/nprogress.js',
        'js/app.js'
    ];

    /** @var string[] */
    public $depends = [
        'yii\web\YiiAsset',
        'app\assets\DateRangePickerAsset',
        'app\assets\AnimateAsset',
    ];
}
