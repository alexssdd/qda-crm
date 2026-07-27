<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\core\helpers\PhoneHelper;
use app\modules\auth\models\User;
use app\modules\auth\helpers\UserHelper;

/**
 * Class UserCreateForm
 * @package app\forms
 */
class UserCreateForm extends Model
{
    public ?string $name = null;
    public ?string $phone = null;
    public string $country = 'kz';
    public ?string $role = null;
    public int $status = UserHelper::STATUS_ACTIVE;
    public ?string $password = null;
    public ?string $passwordRepeat = null;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['phone'], 'filter', 'filter' => [PhoneHelper::class, 'getCleanNumber']],
            [['country'], 'filter', 'filter' => static fn($value) => mb_strtolower(trim((string) $value))],
            [['name'], 'trim'],

            [['phone', 'role', 'name', 'country', 'status'], 'required'],
            [['phone'], 'string', 'max' => 21],
            [['phone'], 'match',
                'pattern' => '/^7\d{10}$/',
                'message' => Yii::t('user', 'Phone must contain 11 digits and start with 7.'),
                'enableClientValidation' => false,
            ],
            [['name'], 'string', 'max' => 255],
            [['country'], 'match', 'pattern' => '/^[a-z]{2,3}$/'],
            [['role'], 'in', 'range' => array_keys(UserHelper::getCreateRoleArray())],
            [['status'], 'in', 'range' => array_keys(UserHelper::getStatusArray())],
            [['phone'], 'unique', 'targetClass' => User::class, 'targetAttribute' => 'phone'],

            [['password', 'passwordRepeat'], 'required'],
            [['password', 'passwordRepeat'], 'string', 'min' => 8, 'max' => 255],
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
