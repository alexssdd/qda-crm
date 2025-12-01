<?php
namespace app\commands;

use Yii;
use Exception;
use yii\rbac\PhpManager;
use yii\console\ExitCode;
use yii\console\Controller;
use app\core\helpers\UserHelper;

/**
 * Class RbacController
 * @package console\controllers
 */
class RbacController extends Controller
{
    /**
     * @return int|void
     * @throws Exception
     */
    public function actionInit()
    {
        if (!$this->confirm("Are you sure? It will re-create permissions tree.")) {
            return ExitCode::OK;
        }

        /** @var $auth PhpManager */
        $auth = Yii::$app->getAuthManager();
        $auth->removeAll();

        // Admin
        $admin = $auth->createRole(UserHelper::ROLE_ADMIN);
        $admin->description = UserHelper::getRoleName(UserHelper::ROLE_ADMIN);
        $auth->add($admin);

        // Admin
        $bot = $auth->createRole(UserHelper::ROLE_BOT);
        $bot->description = UserHelper::getRoleName(UserHelper::ROLE_BOT);
        $auth->add($bot);

        // Administrator
        $administrator = $auth->createRole(UserHelper::ROLE_ADMINISTRATOR);
        $administrator->description = UserHelper::getRoleName(UserHelper::ROLE_ADMINISTRATOR);
        $auth->add($administrator);

        // Operator
        $operator = $auth->createRole(UserHelper::ROLE_OPERATOR);
        $operator->description = UserHelper::getRoleName(UserHelper::ROLE_OPERATOR);
        $auth->add($operator);

        // Marketing
        $marketing = $auth->createRole(UserHelper::ROLE_MARKETING);
        $marketing->description = UserHelper::getRoleName(UserHelper::ROLE_MARKETING);
        $auth->add($marketing);

        // Service Delivery
        $serviceDelivery = $auth->createRole(UserHelper::ROLE_SERVICE_DELIVERY);
        $serviceDelivery->description = UserHelper::ROLE_SERVICE_DELIVERY;
        $auth->add($serviceDelivery);

        // Administrator children
        $auth->addChild($administrator, $operator);
        $auth->addChild($administrator, $marketing);

        // Admin children
        $auth->addChild($admin, $bot);
        $auth->addChild($admin, $administrator);

        $this->stdout('Done!' . PHP_EOL);
    }
}
