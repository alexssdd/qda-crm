<?php

namespace app\services;

use Yii;
use Exception;
use DomainException;
use app\forms\UserUpdateForm;
use app\forms\UserCreateForm;
use app\modules\auth\enums\AuthMethod;
use app\modules\auth\models\AuthIdentity;
use app\modules\auth\models\User;

/**
 * User service
 */
class UserService
{
    /**
     * @param UserCreateForm $form
     * @return User
     * @throws Exception
     */
    public function create(UserCreateForm $form): User
    {
        return Yii::$app->db->transaction(function () use ($form): User {
            $model = new User();
            $model->role = $form->role;
            $model->phone = $form->phone;
            $model->country = $form->country;
            $model->name = $form->name;
            $model->status = $form->status;
            $model->created_at = time();

            if (!$model->save(false)) {
                throw new DomainException('Не удалось создать пользователя.');
            }

            $this->savePassword($model, $form->password);
            $this->saveOtp($model);

            return $model;
        });
    }

    /**
     * @param User $model
     * @param UserUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(User $model, UserUpdateForm $form): void
    {
        Yii::$app->db->transaction(function () use ($model, $form): void {
            $model->name = $form->name;
            $model->country = $form->country;
            $model->role = $form->role;
            $model->status = $form->status;

            if (!$model->save(false)) {
                throw new DomainException('Не удалось обновить пользователя.');
            }

            if ($form->password !== null && $form->password !== '') {
                $this->savePassword($model, $form->password);
            }
        });
    }

    private function savePassword(User $user, string $password): void
    {
        $identity = AuthIdentity::findOne([
            'user_id' => $user->id,
            'type' => AuthMethod::PASSWORD->value,
        ]);

        if ($identity === null) {
            $identity = new AuthIdentity();
            $identity->user_id = $user->id;
            $identity->type = AuthMethod::PASSWORD->value;
            $identity->identifier = $user->phone;
            $identity->created_at = time();
        }

        $identity->credential = Yii::$app->security->generatePasswordHash($password);
        $identity->verified = true;
        $identity->verified_at = time();

        if (!$identity->save(false)) {
            throw new DomainException('Не удалось сохранить пароль пользователя.');
        }
    }

    private function saveOtp(User $user): void
    {
        $identity = AuthIdentity::findOne([
            'user_id' => $user->id,
            'type' => AuthMethod::OTP->value,
        ]);

        if ($identity === null) {
            $identity = new AuthIdentity();
            $identity->user_id = $user->id;
            $identity->type = AuthMethod::OTP->value;
            $identity->identifier = $user->phone;
            $identity->created_at = time();
        }

        $identity->verified = true;
        $identity->verified_at = time();

        if (!$identity->save(false)) {
            throw new DomainException('Не удалось настроить OTP пользователя.');
        }
    }
}
