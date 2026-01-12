<?php

namespace app\modules\auth\forms\otp;

use app\core\forms\Form;
use app\modules\auth\enums\AuthMethod;
use app\modules\auth\models\AuthIdentity;

class OtpRequestForm extends Form
{
    public string $phone;

    private ?AuthIdentity $_identity = null;

    public function rules(): array
    {
        return [
            ['phone', 'required'],
            ['phone', 'match', 'pattern' => '/^\+\d{10,15}$/'],
        ];
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
}