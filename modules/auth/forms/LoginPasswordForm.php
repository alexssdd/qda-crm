<?php

namespace app\modules\auth\forms;

use Yii;
use app\core\forms\Form;
use app\modules\auth\models\User;
use app\core\helpers\PhoneHelper;
use app\modules\auth\enums\AuthMethod;
use app\modules\auth\models\AuthIdentity;

class LoginPasswordForm extends Form
{
    public $phone;
    public $password;

    private $_user = null;

    public function rules(): array
    {
        return [
            [['phone', 'password'], 'required', 'message' => 'Заполните поле'],

            ['phone', 'filter', 'filter' => function ($value) {
                return PhoneHelper::getCleanNumber($value);
            }],

            ['phone', 'validateUser'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Валидатор: Проверяем, существует ли пользователь
     */
    public function validateUser($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $this->_user = User::findByPhone($this->phone);

            if (!$this->_user) {
                $this->addError($attribute, 'ERROR_USER_NOT_FOUND_OR_WRONG_PASSWORD');
            }
        }
    }

    /**
     * Валидатор: Проверяем пароль
     */
    public function validatePassword($attribute): void
    {
        if (!$this->hasErrors()) {
            if (!$this->_user) {
                return;
            }

            $identity = AuthIdentity::findOne([
                'user_id' => $this->_user->id,
                'type' => AuthMethod::PASSWORD->value
            ]);

            if (!$identity || !Yii::$app->security->validatePassword($this->password, $identity->credential)) {
                $this->addError($attribute, 'ERROR_USER_NOT_FOUND_OR_WRONG_PASSWORD');
            }
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->_user);
        }
        return false;
    }
}