<?php

namespace app\services;

use Exception;
use DomainException;
use app\entities\User;
use app\forms\UserUpdateForm;
use app\forms\UserCreateForm;
use app\core\helpers\UserHelper;
use app\core\helpers\PhoneHelper;

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
        $model = new User();
        $model->role = $form->role;
        $model->phone = PhoneHelper::getCleanNumber($form->phone);
        $model->email = $form->email;
        $model->full_name = $form->full_name;
        $model->status = $form->status;
        $model->state = $form->state;
        $model->created_at = time();
        $model->updated_at = time();
        $model->config = [
            'telegram_id' => $form->telegram_id
        ];

        // Password
        $model->generateAuthKey();
        $model->setPassword($form->password);

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }

        return $model;
    }

    /**
     * @param User $model
     * @param UserUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(User $model, UserUpdateForm $form): void
    {
        $model->full_name = $form->full_name;
        $model->role = $form->role;
        $model->status = $form->status;
        $model->state = $form->state;
        $model->updated_at = time();

        $config = $model->config;
        $config['telegram_id'] = $form->telegram_id;
        $model->config = $config;

        // Password
        if ($form->password) {
            $model->setPassword($form->password);
            $model->generateAuthKey();
            $model->removeVerifiedToken();
        }

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }

    /**
     * @param User $model
     * @return void
     * @throws Exception
     */
    public function stateOnline(User $model): void
    {
        if ($model->role !== UserHelper::ROLE_OPERATOR){
            return;
        }

        $model->state = UserHelper::STATE_ONLINE;
        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }

    /**
     * @param User $model
     * @return void
     * @throws Exception
     */
    public function stateOffline(User $model): void
    {
        if ($model->role !== UserHelper::ROLE_OPERATOR){
            return;
        }

        $model->state = UserHelper::STATE_OFFLINE;
        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}