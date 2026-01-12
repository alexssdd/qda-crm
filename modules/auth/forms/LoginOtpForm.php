<?php

namespace app\modules\auth\forms;

use Yii;
use app\core\forms\Form;
use app\modules\auth\models\User;
use app\core\helpers\PhoneHelper;
use app\modules\auth\models\AuthIdentity;
use app\modules\auth\models\AuthOtpCode;
use app\modules\auth\enums\AuthMethod;

class LoginOtpForm extends Form
{
    public string $phone = '';
    public string $code = '';

    private ?AuthIdentity $_identity = null;
    private ?AuthOtpCode $_otpCode = null;
    private ?User $_user = null;

    public function rules(): array
    {
        return [
            [['phone', 'code'], 'required', 'message' => 'Заполните поле'],

            ['phone', 'filter', 'filter' => function ($value) {
                return PhoneHelper::getCleanNumber($value);
            }],

            ['code', 'match', 'pattern' => '/^\d{6}$/', 'message' => 'Код должен содержать 6 цифр'],

            ['phone', 'validateUserAndIdentity'],
            ['code', 'validateOtpCode'],
        ];
    }

    public function validateUserAndIdentity($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $identity = $this->getIdentity();

            if (!$identity) {
                $this->addError($attribute, 'Вход по коду недоступен для этого номера');
                return;
            }

            if (!$identity->user) {
                $this->addError($attribute, 'ERROR_USER_NOT_FOUND_OR_WRONG_OTP');
            }
        }
    }

    public function validateOtpCode($attribute): void
    {
        if (!$this->hasErrors()) {
            $otpCode = $this->getOtpCode();

            if (!$otpCode) {
                $this->addError($attribute, 'ERROR_USER_NOT_FOUND_OR_WRONG_OTP');
                return;
            }

            if ($otpCode->expires_at < time()) {
                $this->addError($attribute, 'ERROR_USER_NOT_FOUND_OR_WRONG_OTP');
                return;
            }

            if (!Yii::$app->security->validatePassword($this->code, $otpCode->code_hash)) {
                $this->addError($attribute, 'ERROR_USER_NOT_FOUND_OR_WRONG_OTP');
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
        if ($this->_otpCode === null && $this->getIdentity()) {
            $this->_otpCode = AuthOtpCode::find()
                ->where(['identity_id' => $this->getIdentity()->id])
                ->one();
        }
        return $this->_otpCode;
    }
}