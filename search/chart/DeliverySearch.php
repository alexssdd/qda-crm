<?php

namespace app\search\chart;

use yii\base\Model;
use app\entities\Order;
use yii\helpers\ArrayHelper;
use app\core\helpers\CityHelper;
use app\core\helpers\OrderHelper;

/**
 * Class DeliverySearch
 * @package app\search\chart
 */
class DeliverySearch extends Model
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
     */
    public function average($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        $result = [];

        // Prepare cities
        $cities = CityHelper::getSelectArray();
        foreach ($cities as $id => $name){
            $result[$id] = [
                'name' => $name,
                'count' => 0,
                'time_total' => 0,
                'time_average' => 0
            ];
        }
        
        /** @var Order[] $data */
        $data = Order::find()
            ->andWhere(['status' => OrderHelper::STATUS_DELIVERED])
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->with(['histories'])
            ->all();

        // Prepare orders
        foreach ($data as $order) {
            if (!$order->city_id){
                continue;
            }
            if (!$histories = $order->histories){
                continue;
            }
            
            $timeBegin = 0;
            $timeEnd = 0;
            foreach ($histories as $history) {
                if ($history->status_after == OrderHelper::STATUS_SHIPPED){
                    $timeBegin = $history->created_at;
                }
                if ($history->status_after == OrderHelper::STATUS_DELIVERED){
                    $timeEnd = $history->created_at;
                }
            }
            
            if (!$timeBegin || !$timeEnd){
                continue;
            }
            
            $result[$order->city_id]['count']++;
            $result[$order->city_id]['time_total'] += $timeEnd - $timeBegin;
        }
        
        // Prepare average
        foreach ($result as &$item) {
            if (!$item['count']){
                continue;
            }
            
            $item['time_average'] = round($item['time_total'] / $item['count']);
        }
        
        // Sort
        ArrayHelper::multisort($result, 'time_average');

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