<?php

namespace app\modules\auth\commands;

use Yii;
use yii\console\ExitCode;
use yii\console\Controller;
use app\modules\auth\rbac\RoleRegistry;

class RbacController extends Controller
{
    public function actionInit(): int
    {
        $auth = Yii::$app->getAuthManager();
        $expectedRoles = array_keys(RoleRegistry::items());
        $loadedRoles = array_keys($auth->getRoles());
        $missingRoles = array_diff($expectedRoles, $loadedRoles);

        if ($missingRoles) {
            $this->stderr('Missing RBAC roles: ' . implode(', ', $missingRoles) . PHP_EOL);
            return ExitCode::CONFIG;
        }

        $this->stdout('RBAC roles loaded.' . PHP_EOL);
        return ExitCode::OK;
    }
}
