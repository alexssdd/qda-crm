<?php

namespace app\modules\auth\forms\otp;

use app\core\forms\Form;
use app\modules\auth\models\User;
use app\modules\auth\models\AuthOtpCode;
use app\modules\auth\enums\AuthMethod;
use app\modules\auth\models\AuthIdentity;

class OtpVerifyForm extends Form
{
    public string $phone;
    public string $code;

    private ?AuthIdentity $_identity = null;
    private ?AuthOtpCode $_otpCode = null;

    public function rules(): array
    {
        return [
            [['phone', 'code'], 'required'],
            ['phone', 'match', 'pattern' => '/^\+\d{10,15}$/'],
            ['code', 'match', 'pattern' => '/^\d{6}$/'],
            ['phone', 'validateIdentity'],
            ['code', 'validateCode'],
        ];
    }

    public function validateIdentity(string $attribute, $params): void
    {
        if (!$this->hasErrors() && !$this->getIdentity()) {
            $this->addError($attribute, 'Неверный или просроченный код');
        }
    }

    public function validateCode(string $attribute): void
    {
        if (!$this->hasErrors()) {
            if (!$otpCode = $this->getOtpCode()) {
                $this->addError($attribute, 'Неверный или просроченный код');
            } elseif ($otpCode->expires_at < time()) {
                $this->addError($attribute, 'Неверный или просроченный код');
            }
        }
    }

    public function getIdentity(): ?AuthIdentity
    {
        if ($this->_identity === null) {
            $this->_identity = AuthIdentity::findOne([
                'type' => AuthMethod::OTP->value,
                'identifier' => $this->phone,
            ]);
        }
        return $this->_identity;
    }

    public function getOtpCode(): ?AuthOtpCode
    {
        if ($this->_otpCode === null) {
            $this->_otpCode = AuthOtpCode::findOne(['identity_id' => $this->getIdentity()->id]);
        }
        return $this->_otpCode;
    }
}