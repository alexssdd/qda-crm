<?php

namespace app\forms\report;

use Yii;
use yii\base\Model;
use DomainException;
use app\entities\Care;
use yii\db\ActiveQuery;
use app\core\forms\Form;

/**
 * Class ReportCareForm
 * @package app\forms\report
 */
class ReportCareForm extends Form
{
    public $date_from;
    public $date_to;
    public $city_id;
    public $channel;
    public $order_number;
    public $status;
    public $handler_id;

    /**
     * ReportCareForm constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->date_from = date('Y-m-d');
        $this->date_to = date('Y-m-d');

        parent::__construct($config);
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['date_from', 'date_to'], 'required'],
            [['date_from', 'date_to', 'city_id', 'channel', 'order_number', 'handler_id', 'status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'date_from' => Yii::t('app', 'Date From'),
            'date_to' => Yii::t('app', 'Date To'),
            'city_id' => Yii::t('app', 'City ID'),
            'channel' => Yii::t('app', 'Channel'),
            'handler_id' => Yii::t('app', 'Handler ID'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getQuery(): ActiveQuery
    {
        $query = Care::find();

        if (!$this->validate()){
            $query->where('0=1');
            return $query;
        }

        if (!$this->downloadAvailable()){
            throw new DomainException('Максимальное можно формировать отчет только за 31 дней');
        }

        $query->andFilterWhere([
            'city_id' => $this->city_id,
            'channel' => $this->channel,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'handler_id' => $this->handler_id,
        ]);

        // Filter by date
        $query
            ->andFilterWhere(['>=', 'created_at', strtotime($this->date_from)])
            ->andFilterWhere(['<=', 'created_at', strtotime($this->date_to . ' 23:59:59')]);

        return $query;
    }

    /**
     * @return bool
     */
    public function downloadAvailable(): bool
    {
        $dateFrom = strtotime($this->date_from);
        $dateTo = strtotime($this->date_to);

        if (!$dateTo){
            $dateTo = strtotime(date('d.m.Y', time()) . ' 23:59');
        }

        if (!$dateFrom){
            return false;
        }
        $diffSeconds = $dateTo - $dateFrom;
        $diffDays = round($diffSeconds / 86400);
        if ($diffDays > 31){
            return false;
        }

        return true;
    }
}