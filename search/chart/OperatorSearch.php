<?php

namespace app\search\chart;

use Exception;
use yii\base\Model;
use app\entities\Order;
use app\search\OrderSearch;
use yii\helpers\ArrayHelper;
use app\core\helpers\UserHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\DeliveryHelper;

/**
 * Class OperatorSearch
 * @package app\search\chart
 */
class OperatorSearch extends Model
{
    /** Attributes */
    public $date_from;
    public $date_to;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['date_from', 'date_to'], 'safe'],
            [['date_from', 'date_to'], 'validatePeriod']
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validatePeriod($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->date_from && !$this->date_to){
                $this->addError($attribute, 'Empty period');
            }
        }
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function longHandle($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        $time = time();
        $result = [];

        /** @var Order[] $data */
        $batch = Order::find()
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->andWhere(['status' => [OrderHelper::STATUS_NEW, OrderHelper::STATUS_ACCEPTED]])
            ->with(['lastAcceptedHistory'])
            ->batch();

        // Prepare orders
        foreach ($batch as $data) {
            foreach ($data as $order) {
                if (!$history = $order->lastAcceptedHistory){
                    continue;
                }

                // 20 min
                if ($time < $history->created_at + (20 * 60)){
                    continue;
                }

                if (!array_key_exists($order->handler_id, $result)){
                    $result[$order->handler_id] = [
                        'name' => $order->handler->full_name,
                        'count_20' => 0,
                        'count_pending' => 0,
                        'url_20' => ['order/index',
                            'handler_id' => $order->handler_id,
                            'date_range' => $this->getDateRange(),
                            'status' => OrderSearch::STATUS_HANDLE,
                        ],
                        'url_pending' => ['order/index',
                            'handler_id' => $order->handler_id,
                            'date_range' => $this->getDateRange(),
                            'status' => OrderSearch::STATUS_PENDING
                        ]
                    ];
                }

                if ($order->isPending()){
                    $result[$order->handler_id]['count_pending']++;
                } else {
                    $result[$order->handler_id]['count_20']++;
                }
            }
        }

        // Sort
        ArrayHelper::multisort($result, 'count', SORT_DESC);

        return array_values($result);
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function withoutAttention($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        if (YII_ENV_DEV || true){
            return  [
                'count' => 15,
                'orders' => array_map(function (){
                    return ['id' => 1, 'number' => 1234567890];
                }, range(1, 15))
            ];
        }

        $result = [
            'count' => 0,
            'orders' => []
        ];
        $bot = UserHelper::getBot();

        /** @var Order[] $data */
        $batch = Order::find()
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->andWhere(['status' => [OrderHelper::STATUS_SHIPPED]])
            ->with(['events'])
            ->batch();

        // Prepare orders
        foreach ($batch as $data) {
            foreach ($data as $order) {
                // Check delay
                if (!$order->isDelay()){
                    continue;
                }

                // Check message author
                $hasOperator = false;
                foreach ($order->events as $event) {
                    if ($event->created_by !== $bot->id){
                        $hasOperator = true;
                    }
                }

                if ($hasOperator){
                    continue;
                }

                $result['count']++;
                $result['orders'][] = [
                    'id' => $order->id,
                    'number' => $order->number
                ];
            }
        }

        return $result;
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function expressSearch($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        if (YII_ENV_DEV || true){
            return  [
                'count' => 15,
                'count_10' => 8,
                'count_20' => 7,
                'orders' => array_map(function (){
                    return ['id' => 1, 'number' => 1234567890];
                }, range(1, 15))
            ];
        }

        $result = [
            'count' => 0,
            'count_10' => 0,
            'count_20' => 0,
            'orders' => []
        ];

        /** @var Order[] $data */
        $batch = Order::find()
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->andWhere(['status' => [OrderHelper::STATUS_SHIPPED]])
            ->andWhere(['delivery_method' => DeliveryHelper::DELIVERY_EXPRESS])
            ->with(['lastShippedHistory'])
            ->batch();

        // Prepare orders
        foreach ($batch as $data) {
            foreach ($data as $order) {
                if (!$history = $order->lastShippedHistory){
                    continue;
                }

                $seconds = time() - $history->created_at;
                if ($seconds < 600){ // 10 minutes
                    continue;
                } elseif ($seconds < 1200){ // 20 minutes
                    $result['count_10']++;
                } else {
                    $result['count_20']++;
                }

                $result['count']++;
                $result['orders'][] = [
                    'id' => $order->id,
                    'number' => $order->number
                ];
            }
        }

        return $result;
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function expressLong($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        if (YII_ENV_DEV || true){
            return  [
                'count' => 15,
                'count_1' => 8,
                'count_1_5' => 7,
                'orders' => array_map(function (){
                    return ['id' => 1, 'number' => 1234567890];
                }, range(1, 15))
            ];
        }

        $result = [
            'count' => 0,
            'count_1' => 0,
            'count_1_5' => 0,
            'orders' => []
        ];

        /** @var Order[] $data */
        $batch = Order::find()
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->andWhere(['status' => [OrderHelper::STATUS_SHIPPED]])
            ->andWhere(['delivery_method' => DeliveryHelper::DELIVERY_EXPRESS])
            ->with(['lastShippedHistory'])
            ->batch();

        // Prepare orders
        foreach ($batch as $data) {
            foreach ($data as $order) {
                if (!$history = $order->lastShippedHistory){
                    continue;
                }

                $seconds = time() - $history->created_at;
                if ($seconds < 3600){ // 1 hours
                    continue;
                } elseif ($seconds < (1.5 * 3600)){ // 1.5 hours
                    $result['count_1']++;
                } else {
                    $result['count_1_5']++;
                }

                $result['count']++;
                $result['orders'][] = [
                    'id' => $order->id,
                    'number' => $order->number
                ];
            }
        }

        return $result;
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function standardLong($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        if (YII_ENV_DEV || true){
            return  [
                'count' => 15,
                'count_2' => 8,
                'count_4' => 7,
                'orders' => array_map(function (){
                    return ['id' => 1, 'number' => 1234567890];
                }, range(1, 15))
            ];
        }

        $result = [
            'count' => 0,
            'count_2' => 0,
            'count_4' => 0,
            'orders' => []
        ];

        /** @var Order[] $data */
        $batch = Order::find()
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->andWhere(['status' => [OrderHelper::STATUS_SHIPPED]])
            ->andWhere(['delivery_method' => DeliveryHelper::DELIVERY_STANDARD])
            ->with(['lastShippedHistory'])
            ->batch();

        // Prepare orders
        foreach ($batch as $data) {
            foreach ($data as $order) {
                // Check delay
                if (!$order->isDelay()){
                    continue;
                }

                // Check history
                if (!$history = $order->lastShippedHistory){
                    continue;
                }

                $seconds = time() - $history->created_at;
                if ($seconds < (2 * 3600)){ // 2 hours
                    continue;
                } elseif ($seconds < (4 * 3600)){ // 4 hours
                    $result['count_2']++;
                } else {
                    $result['count_4']++;
                }

                $result['count']++;
                $result['orders'][] = [
                    'id' => $order->id,
                    'number' => $order->number
                ];
            }
        }

        return $result;
    }

    /**
     * @return false|int
     */
    protected function getDateFrom()
    {
        return strtotime($this->date_from . ' 00:00:00');
    }

    /**
     * @return false|int
     */
    protected function getDateTo()
    {
        return strtotime($this->date_to . ' 23:59:59');
    }

    /**
     * @return string
     */
    protected function getDateRange()
    {
        return $this->date_from . ' 00:00 - ' . $this->date_to . ' 23:59';
    }
}