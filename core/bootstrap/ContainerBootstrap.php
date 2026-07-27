<?php

namespace app\core\bootstrap;

use Yii;
use app\services\ConfigService;
use yii\base\BootstrapInterface;
use app\modules\auth\providers\OtpInterface;
use app\modules\auth\providers\KazInfoTehOtpProvider;

class ContainerBootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        // Config
        Yii::$container->setSingleton(ConfigService::class, function () {
            return new ConfigService();
        });

        Yii::$container->setSingleton(KazInfoTehOtpProvider::class, function ($c) {
            return new KazInfoTehOtpProvider($c->get(ConfigService::class));
        });

        Yii::$container->setSingleton(
            OtpInterface::class,
            function ($c) {
                return $c->get(KazInfoTehOtpProvider::class);
            }
        );
    }
}
