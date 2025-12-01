<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\User;
use app\core\helpers\UserHelper;
use app\core\helpers\PhoneHelper;

/**
 * Class UserUpdateForm
 * @package app\forms
 */
class UserUpdateForm extends Model
{
    public $full_name;
    public $phone;
    public $role;
    public $status;
    public $state;
    public $password;
    public $passwordRepeat;
    public $telegram_id;

    /**
     * @param User $user
     * @param array $config
     */
    public function __construct(User $user, array $config = [])
    {
        $this->phone = PhoneHelper::getMaskPhone($user->phone);
        $this->full_name = $user->full_name;
        $this->role = $user->role;
        $this->status = $user->status;
        $this->state = $user->state;
        $this->telegram_id = UserHelper::getTelegramId($user);

        parent::__construct($config);
    }

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['status', 'state', 'telegram_id'], 'integer'],
            [['role', 'full_name'], 'string'],
            [['role', 'full_name', 'status'], 'required'],

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
            'role' => Yii::t('user', 'Role'),
            'status' => Yii::t('user', 'Status'),
            'state' => Yii::t('user', 'State'),
            'password' => Yii::t('user', 'Password'),
            'passwordRepeat' => Yii::t('user', 'Password Repeat'),
            'telegram_id' => Yii::t('user', 'Telegram Id'),
        ];
    }
}
