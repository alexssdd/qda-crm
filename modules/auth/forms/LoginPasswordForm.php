<?php

namespace app\modules\auth\forms;

use Yii;
use yii\base\Model;
use app\modules\auth\models\User;
use app\core\helpers\PhoneHelper;
use app\modules\auth\enums\AuthMethod;
use app\modules\auth\models\AuthIdentity;

class LoginPasswordForm extends Model
{
    public $phone;
    public $password;
    public $rememberMe = true;

    private $_user = null;

    public function rules(): array
    {
        return [
            [['phone', 'password'], 'required', 'message' => 'Заполните поле'],

            ['phone', 'filter', 'filter' => function ($value) {
                return PhoneHelper::getCleanNumber($value);
            }],

            // Сначала ищем юзера
            ['phone', 'validateUser'],

            // Потом проверяем пароль
            ['password', 'validatePassword'],

            ['rememberMe', 'boolean'],
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
                $this->addError($attribute, 'Пользователь не найден.');
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
                $this->addError($attribute, 'Неверный пароль.');
            }
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->_user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }
}