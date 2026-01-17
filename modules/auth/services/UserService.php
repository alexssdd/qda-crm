<?php

namespace app\modules\auth\services;

use Yii;
use DomainException;
use app\modules\auth\models\User;
use app\core\helpers\PhoneHelper;
use app\modules\auth\enums\AuthMethod;
use app\modules\auth\helpers\UserHelper;
use app\modules\auth\models\AuthIdentity;

class UserService
{
    public function create(string $phone, string $password, string $name, string $country, string $role = 'user'): User
    {
        $cleanPhone = PhoneHelper::getCleanNumber($phone);

        // 1. Создаем модель User
        $user = new User();
        $user->phone = $cleanPhone;
        $user->name = $name;
        $user->country = $country;
        $user->role = $role;
        $user->status = UserHelper::STATUS_ACTIVE;
        $user->created_at = time();

        if (!$user->save(false)) {
            throw new DomainException('Ошибка сохранения пользователя: ' . json_encode($user->errors));
        }

        return $user;
    }

    public function addPasswordIdentity(int $userId, string $identifier, string $password): void
    {
        $identity = new AuthIdentity();
        $identity->user_id = $userId;
        $identity->type = AuthMethod::PASSWORD->value;
        $identity->identifier = $identifier;
        $identity->credential = Yii::$app->security->generatePasswordHash($password);
        $identity->verified = 1;
        $identity->created_at = time();

        if (!$identity->save(false)) {
            throw new DomainException('Ошибка создания пароля: ' . json_encode($identity->errors));
        }
    }

    /**
     * Привязка метода OTP к пользователю
     */
    public function addOtpIdentity(int $userId, string $identifier): void
    {
        $identity = new AuthIdentity();
        $identity->user_id = $userId;
        $identity->type = AuthMethod::OTP->value;
        $identity->identifier = $identifier;
        $identity->verified = 1;
        $identity->created_at = time();

        if (!$identity->save(false)) {
            throw new DomainException('Ошибка настройки OTP: ' . json_encode($identity->errors));
        }
    }
}