<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\User;
use app\core\helpers\UserHelper;
use app\core\helpers\PhoneHelper;
use app\core\validators\PhoneValidator;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $phone;
    public $password;
    public $rememberMe = true;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['phone', 'password'], 'required'],
            ['phone', PhoneValidator::class],
            ['password', 'validatePassword'],
            ['password', 'validateRole'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'password' => Yii::t('app', 'Password'),
            'phone' => Yii::t('app', 'Phone')
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
            }
        }
    }

    /**
     * @param $attribute
     * @return void
     */
    public function validateRole($attribute)
    {
        if(!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user && !in_array($user->role, UserHelper::getDashboardRoles())) {
                $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
            }
        }
    }

    /**
     * @return bool
     */
    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        
        return false;
    }

    /**
     * @return User|null
     */
    protected function getUser(): ?User
    {
        if ($this->_user === null) {
            $phone = PhoneHelper::getCleanNumber($this->phone);
            $this->_user = User::findByPhone($phone);
        }

        return $this->_user;
    }
}
