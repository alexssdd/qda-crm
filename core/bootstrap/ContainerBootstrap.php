<?php

namespace app\core\bootstrap;

use Yii;
use app\services\ConfigService;
use yii\base\BootstrapInterface;
use app\modules\auth\providers\OtpInterface;
use app\modules\auth\providers\FakeOtpProvider;

class ContainerBootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        // Config
        Yii::$container->setSingleton(ConfigService::class, function () {
            return new ConfigService();
        });

        // OTP
        Yii::$container->setSingleton(
            OtpInterface::class,
            function ($c) {
                return new FakeOtpProvider();
            }
        );
    }
}