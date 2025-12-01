<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\User;

/**
 * Class UserCreateForm
 * @package app\forms
 */
class UserCreateForm extends Model
{
    public $full_name;
    public $phone;
    public $email;
    public $role;
    public $status;
    public $password;
    public $passwordRepeat;
    public $state;
    public $telegram_id;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status', 'state', 'telegram_id'], 'integer'],
            [['phone', 'email', 'role', 'full_name'], 'string'],
            [['phone', 'role', 'full_name', 'status'], 'required'],
            [['phone'], 'unique', 'targetClass' => User::class],

            [['password', 'passwordRepeat'], 'required'],
            [['password', 'passwordRepeat'], 'string', 'min' => 8],
            [['password'], 'match', 'pattern' => '/^.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/', 'message' => Yii::t('user', 'Password must contain at least one lower and upper case character and a digit.')],
            [['passwordRepeat'], 'compare', 'compareAttribute' => 'password'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'full_name' => Yii::t('user', 'Full Name'),
            'phone' => Yii::t('user', 'Phone'),
            'email' => Yii::t('user', 'Email'),
            'role' => Yii::t('user', 'Role'),
            'status' => Yii::t('user', 'Status'),
            'password' => Yii::t('user', 'Password'),
            'passwordRepeat' => Yii::t('user', 'Password Repeat'),
            'telegram_id' => Yii::t('user', 'Telegram Id'),
        ];
    }
}
