<?php

namespace app\services\report;

use Yii;
use Exception;
use app\entities\User;
use app\entities\City;
use app\entities\Store;
use app\entities\Order;
use app\entities\Report;
use yii\helpers\ArrayHelper;
use app\core\helpers\DateHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\ReportHelper;
use app\core\helpers\PaymentHelper;
use app\core\helpers\DeliveryHelper;
use yii\base\InvalidConfigException;
use app\forms\report\ReportOrderForm;
use Box\Spout\Common\Entity\Style\Style;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

/**
 * Class ReportOrderService
 * @package app\services\report
 */
class ReportOrderService
{
    private $_model;

    /**
     * VariantService constructor.
     * @param Report $model
     */
    public function __construct(Report $model)
    {
        $this->_model = $model;
    }

    /**
     * @return void
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function generate(): void
    {
        $model = $this->_model;

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile(ReportHelper::getFilePath($model));

        // Default style
        $style = new Style();
        $style->setShouldWrapText(false);
        $writer->setDefaultRowStyle($style);

        $columns = [
            [
                'label' => 'Дата',
                'value' => function ($model){
                    return $model['created_date'];
                }
            ],
            [
                'label' => 'Номер',
                'value' => function ($model){
                    return $model['number'];
                }
            ],
            [
                'label' => 'Город',
                'value' => function ($model){
                    return $model['city'];
                }
            ],
            [
                'label' => 'Канал',
                'value' => function ($model){
                    return $model['channel'];
                }
            ],
            [
                'label' => 'Сумма заказа',
                'value' => function ($model){
                    return $model['cost'];
                }
            ],
            [
                'label' => 'Способ оплаты',
                'value' => function ($model){
                    return $model['payment_method'];
                }
            ],
            [
                'label' => 'Обработал',
                'value' => function ($model){
                    return $model['handler'];
                }
            ],
            [
                'label' => 'Время обработки',
                'value' => function ($model){
                    return $model['handle_time'];
                }
            ],
            [
                'label' => 'Способ доставки',
                'value' => function ($model){
                    return $model['delivery_method'];
                }
            ],
            [
                'label' => 'Номер курьера',
                'value' => function ($model){
                    return $model['courier_phone'];
                }
            ],
            [
                'label' => 'Курьер',
                'value' => function ($model){
                    return $model['courier'];
                }
            ],
            [
                'label' => 'Точки забора',
                'value' => function ($model){
                    return $model['stores'];
                }
            ],
            [
                'label' => 'Номер клиента',
                'value' => function ($model){
                    return $model['phone'];
                }
            ],
            [
                'label' => 'Адрес доставки',
                'value' => function ($model){
                    return $model['address'];
                }
            ],
            [
                'label' => 'Стоимость доставки',
                'value' => function ($model){
                    return $model['delivery_cost'];
                }
            ],
            [
                'label' => 'Статус',
                'value' => function ($model){
                    return $model['status'];
                }
            ],
            [
                'label' => 'Номер заказа партнера',
                'value' => function ($model){
                    return $model['vendor_number'];
                }
            ],
            [
                'label' => 'Время оплаты',
                'value' => function ($model){
                    return $model['payment_time'];
                }
            ],
            [
                'label' => 'Причина отмены',
                'value' => function ($model){
                    return $model['cancel_reason'];
                }
            ],
            [
                'label' => 'Дополнительная причина отмены',
                'value' => function ($model){
                    return $model['cancel_reason_additional'];
                }
            ],
        ];

        // Header
        $headerCells = [];
        foreach ($columns as $column) {
            $value = ArrayHelper::getValue($column, 'label');
            $headerCells[] = $value;
        }

        // Style for header
        $style = new Style();
        $style->setFontBold();

        // Add header
        $headerRow = WriterEntityFactory::createRowFromArray($headerCells, $style);
        $writer->addRow($headerRow);

        // Body
        $form = new ReportOrderForm();
        $query = $form->getQuery($model->getParams());

        // Variables
        $users = User::find()->indexBy('id')->all();
        $stores = Store::find()->indexBy('id')->all();
        $cities = City::find()->indexBy('id')->all();

        /** @var Order $order */
        foreach ($query->batch(500) as $orders) {
            foreach ($orders as $order) {
                $assemblyStores = [];
                $paymentTime = '';

                foreach ($order->stores as $orderStore) {
                    foreach ($orderStore->orderStoreProducts as $orderStoreProduct) {
                        if ($orderStoreProduct->quantity > 0){
                            /** @var Store $store */
                            $store = ArrayHelper::getValue($stores, $orderStore->store_id);
                            if (!$store){
                                continue;
                            }

                            $assemblyStores[] = $store->name;
                        }
                    }
                }

                // Payment
                if ($paymentPaid = $order->paymentPaid){
                    $paymentTime = DateHelper::getGmDate($paymentPaid->created_at - $order->created_at);
                }

                /** @var City $city */
                $city = ArrayHelper::getValue($cities, $order->city_id);
                /** @var User $handler */
                $handler = ArrayHelper::getValue($users, $order->handler_id);
                $cancelReason = $order->cancelReason;

                $item = [
                    'created_date' => Yii::$app->formatter->asDatetime($order->created_at),
                    'number' => $order->number,
                    'city' => $city?->name,
                    'channel' => OrderHelper::getChannel($order->channel),
                    'cost' => (float)$order->amount,
                    'payment_method' => PaymentHelper::getMethodName($order->payment_method),
                    'handler' => $handler ? $handler->full_name : '',
                    'handle_time' => $this->getHandleTime($order),
                    'delivery_method' => DeliveryHelper::getMethodName($order->delivery_method),
                    'courier_phone' => '', // todo
                    'courier' => '', // todo
                    'stores' => $assemblyStores ? implode('; ', array_unique($assemblyStores)) : null,
                    'phone' => $order->phone,
                    'address' => $order->address,
                    'delivery_cost' => (float)$order->delivery_cost,
                    'status' => OrderHelper::getStatusName($order->status),
                    'vendor_number' => $order->vendor_number,
                    'payment_time' => $paymentTime,
                    'cancel_reason' => $cancelReason ? $cancelReason->reason : '',
                    'cancel_reason_additional' => $cancelReason ? $cancelReason->reason_additional : '',
                ];

                $bodyCells = [];
                foreach ($columns as $column) {
                    $value = call_user_func($column['value'], $item);
                    $bodyCells[] = $value;
                }

                $row = WriterEntityFactory::createRowFromArray($bodyCells);
                $writer->addRow($row);
            }
        }

        $writer->close();
    }

    /**
     * @param Order $order
     * @return string
     */
    protected function getHandleTime(Order $order): string
    {
        $startAt = null;
        $endAt = null;

        foreach ($order->histories as $history) {
            // Set start at
            if (!$startAt && $history->status_after == OrderHelper::STATUS_ACCEPTED){
                $startAt = $history->created_at;
            }

            // Set end at
            if (in_array($history->status_after, [OrderHelper::STATUS_SHIPPED, OrderHelper::STATUS_PICKUP])){
                $endAt = $history->created_at;
            }
        }

        if (!$startAt || !$endAt){
            return '';
        }

        return DateHelper::getGmDate($endAt - $startAt);
    }
}