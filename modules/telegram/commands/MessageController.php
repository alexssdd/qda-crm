<?php

namespace app\modules\telegram\commands;

use Exception;
use DomainException;
use app\entities\Order;
use app\entities\User;
use yii\console\Controller;
use app\entities\OrderStore;
use app\services\LogService;
use app\core\helpers\CityHelper;
use app\core\helpers\LogHelper;
use app\core\helpers\TextHelper;
use app\core\helpers\UserHelper;
use app\modules\telegram\services\TelegramService;

class MessageController extends Controller
{
    public function actionOrderTransfer($id, $userId): void
    {
        $target = LogHelper::TARGET_TELEGRAM_ORDER_TRANSFER;

        try {
            $order = $this->getOrder($id);
            $user = $this->getUser($userId);

            if (!$telegramId = UserHelper::getTelegramId($user)) {
                return;
            }

            $message = TextHelper::getTelegramOrderTransfer($id, $order->number);
            (new TelegramService())->send($telegramId, $message);
        } catch (Exception $e) {
            LogService::error($target, ['id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function actionOrderKaspiDelivered($id): void
    {
        $target = LogHelper::TARGET_TELEGRAM_ORDER_KASPI_DELIVERED;

        try {
            $order = $this->getOrder($id);

            $chatMap = [
                CityHelper::ID_AKTAU            => '-1001511446665',
                CityHelper::ID_ASTANA           => '-1001791852909',
                CityHelper::ID_AKTOBE           => '-1001836482532',
                CityHelper::ID_ATYRAU           => '-1001357307012',
                CityHelper::ID_KARAGANDA        => '-1001740902064',
                CityHelper::ID_KOSTANAY         => '-1001804259324',
                CityHelper::ID_PAVLODAR         => '-1001628631771',
                CityHelper::ID_PETROPAVLOVSK    => '-1001755396070',
                CityHelper::ID_SEMEY            => '-1001709863018',
                CityHelper::ID_TARAZ            => '-1001302647206',
                CityHelper::ID_UST_KAMENOGORSK  => '-1002450602250',
                CityHelper::ID_URALSK           => '-1001868416707',
                CityHelper::ID_SHYMKENT         => '-1001800936077',
            ];

            $chatId = $chatMap[$order->city_id] ?? null;

            if (!$orderStore = OrderStore::findOne(['order_id' => $order->id])) {
                throw new DomainException("Order store not found");
            }

            if ($order->city_id == CityHelper::ID_ALMATY && $orderStore->store->number == 'АА_Р_01') {
                $chatId = '-1001502928012';
            }

            if ($order->city_id == CityHelper::ID_ALMATY && $orderStore->store->number == 'АА_Р_15') {
                $chatId = '-1001642881179';
            }

            if (!$chatId) {
                return;
            }

            $message = TextHelper::getTelegramKaspiDelivered($order->number, $order->vendor_number, $orderStore->store->number);

            (new TelegramService())->send($chatId, $message);
        } catch (Exception $e) {
            LogService::error($target, ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function getUser($id): User
    {
        if (!$model = User::findOne($id)) {
            throw new DomainException("User not found id: {$id}");
        }
        return $model;
    }

    protected function getOrder($id): Order
    {
        if (!$model = Order::findOne($id)) {
            throw new DomainException("Order not found id: {$id}");
        }
        return $model;
    }
}