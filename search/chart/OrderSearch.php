<?php

namespace app\search\chart;

use Exception;
use yii\base\Model;
use app\entities\Order;
use app\core\helpers\UserHelper;
use app\core\helpers\OrderHelper;

/**
 * Class OrderSearch
 * @package app\search\chart
 */
class OrderSearch extends Model
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
     * @return array|int[]
     */
    public function averageHandle($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        $result = [
            'count' => 0,
            'time_total' => 0
        ];

        /** @var Order[] $data */
        $batch = Order::find()
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->with(['histories'])
            ->batch(500);

        // Prepare orders
        foreach ($batch as $data) {
            foreach ($data as $order) {
                if (!$histories = $order->histories){
                    continue;
                }

                $timeBegin = 0;
                $timeEnd = 0;
                foreach ($histories as $history) {
                    if (!$timeBegin && $history->status_after == OrderHelper::STATUS_ACCEPTED){
                        $timeBegin = $history->created_at;
                    }
                    if (in_array($history->status_after, [OrderHelper::STATUS_SHIPPED, OrderHelper::STATUS_PICKUP])){
                        $timeEnd = $history->created_at;
                    }
                }

                if (!$timeBegin || !$timeEnd){
                    continue;
                }

                $result['count']++;
                $result['time_total'] += $timeEnd - $timeBegin;
            }
        }

        // Prepare average
        $result['time_average'] = $result['count'] ? round($result['time_total'] / $result['count']) : 0;

        return $result;
    }

    /**
     * @param $params
     * @return int[]
     * @throws Exception
     */
    public function handler($params): array
    {
        $this->load($params);

        $result = [
            'count_total' => 0,
            'count_operator' => 0,
            'count_bot' => 0,
            'percent' => 0,
        ];

        if (!$this->validate()) {
            return $result;
        }

        /** @var Order[] $data */
        $query = Order::find()
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->with(['histories']);

        $batch = $query->batch(500);
        $bot = UserHelper::getBot();

        // Prepare count
        foreach ($batch as $data) {
            foreach ($data as $order) {
                if (!$histories = $order->histories){
                    continue;
                }

                $onlyBot = true;
                foreach ($histories as $history) {
                    if ($history->created_by !== $bot->id){
                        $onlyBot = false;
                        break;
                    }
                }

                $result['count_total']++;
                if ($order->handler_id == $bot->id || $onlyBot){
                    $result['count_bot']++;
                } else {
                    $result['count_operator']++;
                }
            }
        }

        // Prepare percent
        $result['percent'] = $result['count_total'] ? round($result['count_bot'] * 100 / $result['count_total']) : 0;

        return $result;
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function completed($params): array
    {
        $this->load($params);

        $result = [
            'count_total' => 0,
            'count_done' => 0,
            'count_cancel' => 0,
            'percent' => 0,
        ];

        if (!$this->validate()) {
            return $result;
        }

        $query = Order::find()
            ->select([
                'status',
                'COUNT(id) AS count'
            ])
            ->andWhere(['status' => [
                OrderHelper::STATUS_DELIVERED,
                OrderHelper::STATUS_ISSUED,
                OrderHelper::STATUS_CANCELLED
            ]])
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->groupBy(['status'])
            ->asArray();

        $data = $query->all();

        // Prepare count
        foreach ($data as $item) {
            $result['count_total'] += $item['count'];

            if ($item['status'] == OrderHelper::STATUS_CANCELLED){
                $result['count_cancel'] += $item['count'];
            } else {
                $result['count_done'] += $item['count'];
            }
        }

        // Prepare percent
        $result['percent'] = $result['count_total'] ? round($result['count_done'] * 100 / $result['count_total']) : 0;

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
}