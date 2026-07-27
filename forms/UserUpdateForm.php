<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\modules\auth\models\User;
use app\modules\auth\helpers\UserHelper;

/**
 * Class UserUpdateForm
 * @package app\forms
 */
class UserUpdateForm extends Model
{
    private User $user;

    public string $name;
    public string $phone;
    public string $country;
    public string $role;
    public int $status;
    public ?string $password = null;
    public ?string $passwordRepeat = null;

    /**
     * @param User $user
     * @param array $config
     */
    public function __construct(User $user, array $config = [])
    {
        $this->user = $user;
        $this->phone = $user->phone;
        $this->name = $user->name;
        $this->country = $user->country;
        $this->role = $user->role;
        $this->status = $user->status;

        parent::__construct($config);
    }

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['country'], 'filter', 'filter' => static fn($value) => mb_strtolower(trim((string) $value))],
            [['name'], 'trim'],

            [['role', 'name', 'country', 'status'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['country'], 'match', 'pattern' => '/^[a-z]{2,3}$/'],
            [['role'], 'in', 'range' => array_keys($this->getRoleArray())],
            [['status'], 'in', 'range' => array_keys(UserHelper::getStatusArray())],

            [['password', 'passwordRepeat'], 'string', 'min' => 8, 'max' => 255],
            [['password'], 'match', 'pattern' => '/^.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/', 'message' => Yii::t('user', 'Password must contain at least one lower and upper case character and a digit.')],
            [['passwordRepeat'], 'compare', 'compareAttribute' => 'password'],
        ];
    }

    public function getRoleArray(): array
    {
        if ($this->user->role === UserHelper::ROLE_ADMIN) {
            return UserHelper::getRoleArray();
        }

        return UserHelper::getCreateRoleArray();
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('user', 'Name'),
            'phone' => Yii::t('user', 'Phone'),
            'country' => Yii::t('user', 'Country'),
            'role' => Yii::t('user', 'Role'),
            'status' => Yii::t('user', 'Status'),
            'password' => Yii::t('user', 'Password'),
            'passwordRepeat' => Yii::t('user', 'Password Repeat'),
        ];
    }
}
