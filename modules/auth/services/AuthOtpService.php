<?php

namespace app\modules\auth\services;

use Yii;
use DomainException;
use app\modules\auth\enums\AuthMethod;
use app\modules\auth\models\AuthOtpCode;
use app\modules\auth\models\AuthIdentity;
use app\modules\auth\providers\OtpInterface;
use app\modules\auth\forms\otp\OtpVerifyForm;
use app\modules\auth\forms\otp\OtpRequestForm;

class AuthOtpService
{
    public int $ttl = 120;

    private OtpInterface $provider;

    public function __construct(OtpInterface $provider)
    {
        $this->provider = $provider;
    }

    public function request(OtpRequestForm $form, $language): void
    {
        $identity = $form->getIdentity();

        if (!$identity) {
            $identity = new AuthIdentity();
            $identity->type = AuthMethod::OTP->value;
            $identity->identifier = $form->phone;
            $identity->created_at = time();

            if (!$identity->save(false)) {
                throw new DomainException('Ошибка создания идентификатора');
            }
        }

        $code = $this->provider->generateOtp();
        $hash = Yii::$app->security->generatePasswordHash((string)$code);
        $now = time();
        $exp = $now + $this->ttl;

        Yii::$app->db->createCommand()->upsert(
            AuthOtpCode::tableName(),
            [
                'identity_id' => $identity->id,
                'code_hash' => $hash,
                'expires_at' => $exp,
                'created_at' => $now,
            ]
        )->execute();

        // Send code
        $this->provider->sendOtp($form->phone, $code, $language);
    }

    public function verify(OtpVerifyForm $form): void
    {
        $identity = $form->getIdentity();
        $otpCode = $form->getOtpCode();

        if (!Yii::$app->security->validatePassword($form->code, $otpCode->code_hash)) {
            throw new DomainException('Неверный код');
        }

        $otpCode->delete();
        $identity->markVerified();
    }
}