<?php

namespace app\modules\auth;

use Yii;

/**
 * Class Module
 * @package app\modules\auth
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\auth\controllers';

    public function init(): void
    {
        parent::init();

        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\auth\commands';
        }
    }
}