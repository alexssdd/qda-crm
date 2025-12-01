<?php

namespace app\commands;

use Yii;
use app\entities\User;
use yii\helpers\ArrayHelper;
use yii\console\Controller;
use yii\console\Exception;

/**
 * Interactive console roles manager
 */
class RolesController extends Controller
{
    /**
     * Adds role to user
     */
    public function actionAssign()
    {
        $phone = $this->prompt('Phone:', ['required' => true]);
        $user = $this->findModel($phone);
        $roleName = $this->select('Role:', ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description'));
        $authManager = Yii::$app->getAuthManager();
        $role = $authManager->getRole($roleName);
        $authManager->assign($role, $user->id);
        $this->stdout('Done!' . PHP_EOL);
    }

    /**
     * Removes role from user
     */
    public function actionRevoke()
    {
        $phone = $this->prompt('Phone:', ['required' => true]);
        $user = $this->findModel($phone);

        $roleName = $this->select('Role:', ArrayHelper::merge(
            ['all' => 'All Roles'],
            ArrayHelper::map(Yii::$app->authManager->getRolesByUser($user->id), 'name', 'description'))
        );
        $authManager = Yii::$app->getAuthManager();
        if ($roleName == 'all') {
            $authManager->revokeAll($user->id);
        } else {
            $role = $authManager->getRole($roleName);
            $authManager->revoke($role, $user->id);
        }
        $this->stdout('Done!' . PHP_EOL);
    }


    private function findModel($phone)
    {
        if (!$model = User::findOne(['phone' => $phone])) {
            throw new Exception('User is not found');
        }
        return $model;
    }
}