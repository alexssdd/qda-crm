<?php

namespace app\search\chart;

use Yii;
use DateTime;
use Exception;
use DatePeriod;
use DateInterval;
use yii\base\Model;
use app\entities\User;
use app\entities\Order;
use yii\helpers\ArrayHelper;
use app\core\helpers\DateHelper;
use app\core\helpers\UserHelper;
use app\core\helpers\OrderHelper;

/**
 * Class SaleSearch
 * @package app\search\chart
 */
class SaleSearch extends Model
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
    public function channels($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        $query = Order::find()
            ->select([
                'channel',
                'COUNT(id) AS count',
                'SUM(amount) AS sum'
            ])
            ->andWhere(['status' => [
                OrderHelper::STATUS_DELIVERED,
                OrderHelper::STATUS_ISSUED,
            ]])
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->groupBy(['channel'])
            ->asArray();

        $data = $query->all();
        $result = [];
        $totalCount = array_sum(ArrayHelper::getColumn($data, 'count'));

        // Prepare percent
        foreach ($data as $item) {
            $percent = 0;
            if ($totalCount){
                $percent = round($item['count'] * 100 / $totalCount, 2);
            }
            $result[] = [
                'name' => OrderHelper::getChannel($item['channel']),
                'percent' => $percent,
                'count' => $item['count'],
                'sum' => $item['sum']
            ];
        }

        // Sort
        ArrayHelper::multisort($result, 'percent', SORT_DESC);

        return $result;
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function status($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        $query = Order::find()
            ->select([
                'DATE(FROM_UNIXTIME(created_at)) AS date',
                'status',
                'COUNT(id) AS count',
                'SUM(amount) AS sum'
            ])
            ->andWhere(['status' => [
                OrderHelper::STATUS_DELIVERED,
                OrderHelper::STATUS_ISSUED,
                OrderHelper::STATUS_CANCELLED,
            ]])
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->groupBy([
                'DATE(FROM_UNIXTIME(created_at))',
                'status'
            ])
            ->asArray();

        $data = $query->all();
        $dates = $this->getDates();
        $result = [
            'total' => [],
            'cancel' => [],
            'done' => [],
            'categories' => []
        ];

        foreach ($dates as $date) {
            $week = date('w', strtotime($date));
            $name = DateHelper::getWeekShortName($week);
            $result['total'][$date] = [
                'date' => DateHelper::format($date, 'd.m.Y', 'Y-m-d'),
                'name' => $name,
                'count' => 0,
                'sum' => 0,
            ];
            $result['cancel'][$date] = [
                'date' => DateHelper::format($date, 'd.m.Y', 'Y-m-d'),
                'name' => $name,
                'count' => 0,
                'sum' => 0,
            ];
            $result['done'][$date] = [
                'date' => DateHelper::format($date, 'd.m.Y', 'Y-m-d'),
                'name' => $name,
                'count' => 0,
                'sum' => 0,
            ];
            $result['categories'][] = $name;
        }

        // Prepare sum
        foreach ($data as $item) {
            $date = $item['date'];
            if (!in_array($date, $dates)){
                continue;
            }

            // Total
            $result['total'][$date]['count'] += (int)$item['count'];
            $result['total'][$date]['sum'] += (float)$item['sum'];
            $result['total'][$date]['sum_label'] = Yii::$app->formatter->asDecimal($result['total'][$date]['sum']);

            // Cancel
            if ($item['status'] == OrderHelper::STATUS_CANCELLED){
                $result['cancel'][$date]['count'] += (int)$item['count'];
                $result['cancel'][$date]['sum'] += (float)$item['sum'];
                $result['cancel'][$date]['sum_label'] = Yii::$app->formatter->asDecimal($result['cancel'][$date]['sum']);
            } else {
                $result['done'][$date]['count'] += (int)$item['count'];
                $result['done'][$date]['sum'] += (float)$item['sum'];
                $result['done'][$date]['sum_label'] = Yii::$app->formatter->asDecimal($result['done'][$date]['sum']);
            }
        }

        // Prepare result
        $result['total'] = array_values($result['total']);
        $result['cancel'] = array_values($result['cancel']);
        $result['done'] = array_values($result['done']);

        return $result;
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function operator($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        $query = Order::find()
            ->select([
                'handler_id',
                'COUNT(id) AS count',
                'SUM(IF(handler_id = created_by, 1, 0)) AS count_crm',
                'SUM(IF(handler_id = created_by, amount, 0)) AS sum_crm',
            ])
            ->andWhere(['status' => [
                OrderHelper::STATUS_DELIVERED,
                OrderHelper::STATUS_ISSUED
            ]])
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->andWhere(['not', ['handler_id' => UserHelper::getBot()->id]])
            ->groupBy([
                'handler_id'
            ])
            ->orderBy(['sum_crm' => SORT_DESC])
            ->asArray();

        $data = $query->all();
        $users = User::find()->indexBy('id')->all();
        $result = [];

        // Prepare result
        foreach ($data as $item) {
            $handler_id = $item['handler_id'];

            /** @var User $user */
            $user = ArrayHelper::getValue($users, $handler_id);
            if (!$user){
                continue;
            }

            $result[$handler_id] = [
                'name' => $user->full_name,
                'count' => (int)$item['count'],
                'count_crm' => (int)$item['count_crm'],
                'sum_crm' => (int)$item['sum_crm'],
                'count_lead' => 0, // todo
                'count_care' => 0 // todo
            ];
        }

        return $result;
    }

    /**
     * @param $params
     * @return array
     */
    public function month($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        $query = Order::find()
            ->select([
                'YEAR(FROM_UNIXTIME(created_at)) AS year',
                'MONTH(FROM_UNIXTIME(created_at)) AS month',
                'SUM(amount) AS sum'
            ])
            ->andWhere(['status' => [
                OrderHelper::STATUS_DELIVERED,
                OrderHelper::STATUS_ISSUED,
            ]])
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->groupBy([
                'YEAR(FROM_UNIXTIME(created_at))',
                'MONTH(FROM_UNIXTIME(created_at))',
            ])
            ->asArray();

        $data = $query->all();
        $result = [];

        foreach ($data as $item) {
            $result[] = [
                'month' => DateHelper::getMonthName($item['month']) . ', ' . $item['year'],
                'name' => DateHelper::getMonthShortName($item['month']),
                'sum' => $item['sum'],
                'sum_label' => Yii::$app->formatter->asDecimal($item['sum']),
            ];
        }

        return $result;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getDates(): array
    {
        $dates = [];

        $period = new DatePeriod(
            new DateTime($this->date_from),
            new DateInterval('P1D'),
            (new DateTime($this->date_to))->modify('+1 day')
        );

        foreach ($period as $item) {
            $dates[] = $item->format('Y-m-d');
        }

        return $dates;
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