<?php

namespace app\modules\auth\services;

use Yii;
use DomainException;
use app\core\helpers\PhoneHelper;
use app\modules\auth\helpers\UserHelper;
use app\modules\auth\models\AuthOtpCode;
use app\modules\auth\forms\otp\OtpVerifyForm;
use app\modules\auth\forms\otp\OtpRequestForm;

class AuthOtpService
{
    public int $ttl = 120;
    public int $requestCooldown = 60;
    public int $retryWindow = 3600;

    public function __construct(private OtpDeliveryService $delivery)
    {
    }

    public function request(OtpRequestForm $form, $language): void
    {
        $identity = $form->getIdentity();
        $user = $identity?->user;
        $recipient = PhoneHelper::getCleanNumber($form->phone);

        if (
            !$identity
            || !$user
            || $user->status !== UserHelper::STATUS_ACTIVE
            || !UserHelper::isCrmLoginRole($user->role)
            || $user->phone !== $recipient
        ) {
            throw new DomainException('OTP недоступен');
        }

        $mutex = Yii::$app->mutex;
        $lockName = 'crm_otp_request_' . $identity->id;
        if (!$mutex->acquire($lockName, 3)) {
            return;
        }

        try {
            $now = time();
            $existingCode = AuthOtpCode::findOne(['identity_id' => $identity->id]);
            if ($existingCode && $existingCode->created_at + $this->requestCooldown > $now) {
                return;
            }

            $isRetry = $existingCode
                && $existingCode->created_at + $this->retryWindow > $now;
            $code = $this->delivery->generateOtp();
            $hash = Yii::$app->security->generatePasswordHash((string)$code);

            Yii::$app->db->createCommand()->upsert(
                AuthOtpCode::tableName(),
                [
                    'identity_id' => $identity->id,
                    'code_hash' => $hash,
                    'expires_at' => $now + $this->ttl,
                    'created_at' => $now,
                    'verify_attempts' => 0,
                ]
            )->execute();

            try {
                $this->delivery->sendOtp(
                    $form->phone,
                    $code,
                    $language,
                    $isRetry
                );
            } catch (\Throwable $e) {
                AuthOtpCode::deleteAll([
                    'identity_id' => $identity->id,
                    'code_hash' => $hash,
                ]);
                throw $e;
            }
        } finally {
            $mutex->release($lockName);
        }
    }

    public function verify(OtpVerifyForm $form): void
    {
        $identity = $form->getIdentity();
        $otpCode = $form->getOtpCode();

        if (!Yii::$app->security->validatePassword($form->code, $otpCode->code_hash)) {
            $otpCode->registerFailedAttempt();
            throw new DomainException('Неверный код');
        }

        $otpCode->delete();
        $identity->markVerified();
    }
}
