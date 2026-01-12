<?php

namespace app\modules\auth\commands;

use yii\console\Controller;
use app\modules\auth\services\AuthOtpService;
use app\modules\auth\forms\otp\OtpRequestForm;

class OtpController extends Controller
{
    private AuthOtpService $service;

    public function __construct($id, $module, AuthOtpService $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function actionRequest($phone, $language = 'ru'): void
    {
        $form = new OtpRequestForm();
        $form->phone = $phone;

        $this->service->request($form, $language);
    }
}