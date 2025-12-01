<?php

namespace app\commands;

use Exception;
use Throwable;
use DomainException;
use app\entities\User;
use app\entities\Order;
use yii\console\Controller;
use app\services\LogService;
use app\core\helpers\LogHelper;
use app\core\helpers\UserHelper;
use yii\db\StaleObjectException;
use app\core\helpers\OrderHelper;
use app\services\order\OrderBotService;
use app\services\order\OrderSyncService;
use app\services\order\OrderManageService;
use app\services\order\OrderHistoryService;
use app\services\order\OrderAssemblyContinueService;

/**
 * Order controller
 */
class OrderController extends Controller
{
    /**
     * @param $id
     * @param $status
     * @param $userId
     * @return void
     * @throws Exception
     */
    public function actionSync($id, $status, $userId): void
    {
        $target = LogHelper::TARGET_ORDER_SYNC;
        $start = microtime(true);

        try {
            $order = $this->getOrder($id);
            $user = $this->getUser($userId);

            (new OrderSyncService($order, $user))->run($status);
        } catch (Exception $e) {
            LogService::error($target, ['id' => $id, 'error' => $e->getMessage()], $start);
            throw $e;
        }
    }

    /**
     * @param $id
     * @return void
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionBot($id): void
    {
        $target = LogHelper::TARGET_ORDER_BOT;
        $start = microtime(true);

        try {
            $order = $this->getOrder($id);
            $user = UserHelper::getBot();

            (new OrderBotService($order, $user))->handle();
        } catch (Exception $e) {
            LogService::error($target, ['id' => $id, 'error' => $e->getMessage()], $start, true);
            throw $e;
        }
    }

    /**
     * @param $id
     * @param $storeId
     * @return void
     * @throws Throwable
     */
    public function actionAssemblyContinue($id, $storeId): void
    {
        $target = LogHelper::TARGET_ORDER_ASSEMBLY_CONTINUE;
        $start = microtime(true);

        try {
            $order = $this->getOrder($id);
            $user = UserHelper::getBot();

            (new OrderAssemblyContinueService($order, $user, $storeId))->run();
        } catch (Exception $e) {
            LogService::error($target, ['id' => $id, 'error' => $e->getMessage()], $start);
            throw $e;
        }
    }

    /**
     * @param $id
     * @return void
     * @throws Exception
     */
    public function actionCancel($id): void
    {
        $order = $this->getOrder($id);
        $prevStatus = $order->status;

        (new OrderManageService($order))->cancelled();
        (new OrderHistoryService($order, UserHelper::getBot()))->create($prevStatus, OrderHelper::STATUS_CANCELLED);
    }

    /**
     * @param $id
     * @return User
     */
    protected function getUser($id): User
    {
        if (!$model = User::findOne($id)) {
            throw new DomainException("User id: $id, not found");
        }
        return $model;
    }

    /**
     * @param $id
     * @return Order
     */
    protected function getOrder($id): Order
    {
        if (!$model = Order::findOne($id)) {
            throw new DomainException("Order id: $id not found");
        }
        return $model;
    }
}