<?php

namespace app\core\bootstrap;

use Yii;
use app\services\ConfigService;
use yii\base\BootstrapInterface;
use app\modules\auth\services\OtpDeliveryService;
use app\modules\auth\providers\MessaggioOtpProvider;
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

        Yii::$container->setSingleton(MessaggioOtpProvider::class, function ($c) {
            return new MessaggioOtpProvider($c->get(ConfigService::class));
        });

        Yii::$container->setSingleton(OtpDeliveryService::class, function ($c) {
            return new OtpDeliveryService(
                $c->get(ConfigService::class),
                $c->get(MessaggioOtpProvider::class),
                $c->get(KazInfoTehOtpProvider::class),
            );
        });
    }
}
