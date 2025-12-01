<?php

namespace app\search;

use Yii;
use yii\base\Model;
use app\entities\Care;
use app\core\helpers\UserHelper;
use app\core\helpers\CareHelper;
use app\core\helpers\PhoneHelper;
use yii\data\ActiveDataProvider;
use app\core\behaviors\DateRangeBehavior;

/**
 * Care search
 */
class CareSearch extends Model
{
    /** Statuses */
    const STATUS_COMPLETED = 901;
    const STATUS_HANDLE = 902;

    /** Default */
    const DEFAULT_LIKE_LENGTH = 8;

    public $id;
    public $number;
    public $customer;
    public $name;
    public $phone;
    public $city_id;
    public $type;
    public $channel;
    public $cost;
    public $status;
    public $handler_id;
    public $language;
    public $count_request;
    public $count_problem;
    public $delivery_late;
    public $complaint_object;
    public $order_number;
    public $transferred;
    public $my;

    public $date_range;
    public $date_from;
    public $date_to;

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'dateStartAttribute' => 'date_from',
                'dateEndAttribute' => 'date_to',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [[
                'id', 'number', 'city_id', 'type', 'channel', 'status', 'handler_id', 'language', 'count_request',
                'count_problem', 'delivery_late', 'complaint_object', 'order_number'
            ], 'integer'],
            [['customer', 'name', 'phone', 'cost', 'transferred', 'my'], 'safe'],
            ['date_range', 'match', 'pattern' => '/^.+\s\-\s.+$/']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'handler_id' => Yii::t('care', 'Handler ID'),
            'language' => Yii::t('care', 'Language'),
            'type' => Yii::t('care', 'Type'),
            'order_number' => Yii::t('care', 'Order Number'),
            'count_request' => Yii::t('care', 'Count Request'),
            'count_problem' => Yii::t('care', 'Count Problem'),
            'delivery_late' => Yii::t('care', 'Delivery Late'),
            'complaint_object' => Yii::t('care', 'Complaint Object')
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Care::find();

        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ],
            'pagination' => [
                'defaultPageSize' => 22
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $provider;
        }

        // Filter by equal
        $query->andFilterWhere([
            'city_id' => $this->city_id,
            'handler_id' => $this->handler_id,
            'type' => $this->type,
            'channel' => $this->channel,
            'language' => $this->language,
            'count_request' => $this->count_request,
            'count_problem' => $this->count_problem,
            'delivery_late' => $this->delivery_late,
            'complaint_object' => $this->complaint_object,
        ]);

        // Filter by date
        $query->andFilterWhere(['between', 'created_at', $this->date_from, $this->date_to]);

        // Filter by text
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', PhoneHelper::getCleanNumber($this->phone)]);

        // Filter by transferred
        if ($this->transferred){
            $query->andWhere(['not', ['executor_id' => null]]);
        }

        if ($this->my) {
            $user = UserHelper::getIdentity();
            $query->andWhere(['or',
                ['handler_id' => $user->id],
                ['executor_id' => $user->id],
            ]);
        }

        // Filter by status
        if ($this->status == self::STATUS_COMPLETED){
            $query
                ->andWhere(['status' => [
                    CareHelper::STATUS_FINISHED_GOOD,
                    CareHelper::STATUS_FINISHED_BAD,
                    CareHelper::STATUS_COULD_NOT_CALL
                ]]);
        } elseif ($this->status == self::STATUS_HANDLE){
            $query
                ->andWhere(['status' => [
                    CareHelper::STATUS_NEW,
                    CareHelper::STATUS_ACCEPTED,
                    CareHelper::STATUS_WAITING,
                ]]);
        } else {
            $query->andFilterWhere(['status' => $this->status]);
        }

        // Optimize like search
        if ($this->number && strlen($this->number) < self::DEFAULT_LIKE_LENGTH) {
            $query->andFilterWhere(['like', 'number', $this->number]);
        } else {
            $query->andFilterWhere(['number' => $this->number]);
        }

        // Optimize like search
        if ($this->order_number && strlen($this->order_number) < self::DEFAULT_LIKE_LENGTH) {
            $query->andFilterWhere(['like', 'order_number', $this->order_number]);
        } else {
            $query->andFilterWhere(['order_number' => $this->order_number]);
        }

        return $provider;
    }

    /**
     * @return bool
     */
    public function isFilterUsed(): bool
    {
        $attributes = [
            'handler_id',
            'language',
            'count_request',
            'count_problem',
            'delivery_late',
            'complaint_object',
            'order_number',
            'transferred',
        ];

        foreach ($attributes as $attribute) {
            if (!empty($this->$attribute)){
                return true;
            }
        }

        return false;
    }
}
