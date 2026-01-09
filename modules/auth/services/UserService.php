<?php

namespace app\modules\auth\services;

use Yii;
use Exception;
use DomainException;
use app\modules\auth\models\User;
use app\core\helpers\PhoneHelper;
use app\modules\auth\enums\AuthMethod;
use app\modules\auth\models\AuthIdentity;

class UserService
{
    public function create(string $phone, string $password, string $name, string $country, string $role = 'user'): User
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $cleanPhone = PhoneHelper::getCleanNumber($phone);

            // 1. Создаем модель User
            $user = new User();
            $user->phone = $cleanPhone;
            $user->name = $name;
            $user->country = $country;
            $user->role = $role;
            $user->status = 10; // Active
            $user->created_at = time();

            if (!$user->save(false)) {
                throw new DomainException('Ошибка сохранения пользователя: ' . json_encode($user->errors));
            }

            // 2. Добавляем возможность входа по паролю
            $this->addPasswordIdentity($user->id, $cleanPhone, $password);

            // 3. Добавляем возможность входа по OTP (резервируем)
            $this->addOtpIdentity($user->id, $cleanPhone);

            $transaction->commit();
            return $user;

        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
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
        $identity->verified = 1; // Считаем телефон подтвержденным, если админ создал
        $identity->created_at = time();

        if (!$identity->save(false)) {
            throw new DomainException('Ошибка настройки OTP: ' . json_encode($identity->errors));
        }
    }
}