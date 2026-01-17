<?php

namespace app\modules\auth\commands;

use Exception;
use DomainException;
use yii\console\ExitCode;
use yii\console\Controller;
use app\modules\auth\models\User;
use app\modules\auth\helpers\UserHelper;
use app\modules\auth\services\UserService;

class UserController extends Controller
{
    public function actionCreate(): int
    {
        $service = new UserService();

        try {
            echo "Создание пользователя ...\n";

            $phone = $this->prompt('Phone:', ['required' => true]);
            $name = $this->prompt('Name:', ['required' => true]);
            $role = $this->prompt('Role:', ['required' => true, 'default' => UserHelper::ROLE_ADMIN]);
            $country = $this->prompt('Role:', ['required' => true, 'default' => 'kz']);

            $user = $service->create($phone, $name, $country, $role);

            echo "Пользователь ID: {$user->id} создан.\n";
            return ExitCode::OK;

        } catch (Exception $e) {
            echo "Ошибка: " . $e->getMessage() . "\n";
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    public function actionAddOtpIdentity($phone): void
    {
        $model = $this->getUser($phone);
        (new UserService())->addOtpIdentity($model->id, $model->phone);
    }

    public function actionAddPasswordIdentity($phone): void
    {
        $model = $this->getUser($phone);
        (new UserService())->addPasswordIdentity($model->id, $model->phone, $model->phone);
    }

    protected function getUser($phone): User
    {
        if (!$model = User::findOne(['phone' => $phone])) {
            throw new DomainException("User phone: {$phone} not found");
        }

        return $model;
    }
}