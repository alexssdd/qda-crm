<?php

namespace app\modules\auth\commands;

use Exception;
use yii\console\ExitCode;
use yii\console\Controller;
use app\modules\auth\services\UserService;

class UserController extends Controller
{
    public function actionCreate($phone, $password, $name = 'Admin', $country = 'kz'): int
    {
        $service = new UserService();

        try {
            echo "Создание пользователя $phone...\n";

            $user = $service->create($phone, $password, $name, $country, 'admin');

            echo "Пользователь ID: {$user->id} создан.\n";
            return ExitCode::OK;

        } catch (Exception $e) {
            echo "Ошибка: " . $e->getMessage() . "\n";
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}