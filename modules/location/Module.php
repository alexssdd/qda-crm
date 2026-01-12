<?php

namespace app\modules\location;

use Yii;
use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public function init(): void
    {
        parent::init();

        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\location\commands';
        }
    }

    public function bootstrap($app): void
    {
        // Правила только для веб‑приложения
        if (!$app instanceof \yii\web\Application) {
            return;
        }
    }
}