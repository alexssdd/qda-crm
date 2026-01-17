<?php

namespace app\modules\auth\commands;

use Yii;
use yii\rbac\PhpManager;
use yii\console\ExitCode;
use yii\console\Controller;
use app\modules\auth\helpers\UserHelper;

class RbacController extends Controller
{
    public function actionInit()
    {
        if (!$this->confirm("Are you sure? It will re-create permissions tree.")) {
            return ExitCode::OK;
        }

        /** @var $auth PhpManager */
        $auth = Yii::$app->getAuthManager();
        $auth->removeAll();

        $roleUserVendor = $auth->createRole(UserHelper::ROLE_ADMIN);
        $roleUserVendor->description = UserHelper::ROLE_ADMIN;
        $auth->add($roleUserVendor);

        $this->stdout('Done!' . PHP_EOL);
    }
}