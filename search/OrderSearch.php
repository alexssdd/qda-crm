<?php

namespace app\search;

use app\core\helpers\DataHelper;
use app\core\helpers\OrderReceiptHelper;
use app\entities\OrderReceipt;
use Yii;
use yii\base\Model;
use app\modules\order\models\Order;
use yii\data\ActiveDataProvider;
use app\core\helpers\UserHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\PhoneHelper;
use app\core\behaviors\DateRangeBehavior;

/**
 * Order search
 */
class OrderSearch extends Model
{
    /** Statuses */
    const STATUS_COMPLETED = 901;
    const STATUS_HANDLE = 902;
    const STATUS_PENDING = 903;

    /** Default */
    const DEFAULT_LIKE_LENGTH = 8;

    public $id;
    public $number;
    public $vendor_number;
    public $vendor_id;
    public $delivery_method;
    public $payment_method;
    public $name;
    public $phone;
    public $city_id;
    public $store_id;
    public $channel;
    public $cost;
    public $status;
    public $handler_id;
    public $transferred;
    public $event;
    public $my;
    public $receipt_sale;
    public $receipt_return;
    public $sku;
    public $account_wb;
    public $account_ozon;

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
                'id', 'number', 'city_id', 'store_id', 'channel', 'status', 'handler_id', 'delivery_method',
                'payment_method', 'event', 'account_wb', 'account_ozon'
            ], 'integer'],
            [[
                'name', 'phone', 'cost', 'vendor_id', 'vendor_number', 'transferred', 'my', 'receipt_sale',
                'receipt_return', 'sku', 'cost'
            ], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/']
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Order::find()
            ->alias('orders')
            ->with(['histories']);

        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $provider;
        }

        // Filter by equal
        $query->andFilterWhere([
            'orders.store_id' => $this->store_id,
            'orders.channel' => $this->channel,
            'orders.handler_id' => $this->handler_id,
            'orders.vendor_id' => $this->vendor_id,
            'orders.vendor_number' => $this->vendor_number,
            'orders.payment_method' => $this->payment_method,
            'orders.delivery_method' => $this->delivery_method,
            'orders.amount' => $this->cost,
        ]);

        // Account
        $query->andFilterWhere(['orders.account_id' => $this->account_wb]);
        $query->andFilterWhere(['orders.account_id' => $this->account_ozon]);

        // Filter by date
        $query->andFilterWhere(['between', 'orders.created_at', $this->date_from, $this->date_to]);

        // Filter by text
        $query->andFilterWhere(['like', 'orders.name', $this->name]);

        // Filter by phone
        if ($this->phone){
            $query->andWhere(['like', 'orders.phone', PhoneHelper::getCleanNumber($this->phone)]);
        }

        // Filter by transferred
        if ($this->transferred){
            $query->andWhere(['not', ['orders.executor_id' => null]]);
        }

        // Filter by user
        if ($this->my) {
            $user = UserHelper::getIdentity();
            $query->andWhere(['or',
                ['orders.handler_id' => $user->id],
                ['orders.executor_id' => $user->id],
            ]);
        }

        // Filter by status
        if ($this->status == self::STATUS_COMPLETED){
            $query
                ->andWhere(['orders.status' => [
                    OrderHelper::STATUS_DELIVERED,
                    OrderHelper::STATUS_ISSUED
                ]]);
        } elseif ($this->status == self::STATUS_HANDLE){
            $query
                ->andWhere(['orders.status' => [
                    OrderHelper::STATUS_NEW,
                    OrderHelper::STATUS_ACCEPTED
                ]]);
        } elseif ($this->status == self::STATUS_PENDING){
            $query
                ->andWhere(['orders.status' => [
                    OrderHelper::STATUS_NEW,
                    OrderHelper::STATUS_ACCEPTED,
                    OrderHelper::STATUS_SHIPPED,
                ]])
                ->andWhere('JSON_EXTRACT(orders.extra_fields, "$.is_pending") = TRUE');
        } else {
            $query->andFilterWhere(['orders.status' => $this->status]);
        }

        // Optimize like search
        if ($this->number && strlen($this->number) < self::DEFAULT_LIKE_LENGTH) {
            $query->andFilterWhere(['like', 'orders.number', $this->number]);
        } else {
            $query->andFilterWhere(['orders.number' => $this->number]);
        }

        // Event
        if ($this->event){
            $query->joinWith(['events events'])
                ->andWhere(['events.type' => $this->event])
                ->groupBy(['orders.id']);
        }

        // Event
        if ($this->sku){
            $query->joinWith(['products products'])
                ->andWhere(['products.sku' => $this->sku])
                ->groupBy(['orders.id']);
        }

        return $provider;
    }

    /**
     * @return bool
     */
    public function isFilterUsed(): bool
    {
        $attributes = [
            'vendor_number',
            'vendor_id',
            'delivery_method',
            'payment_method',
            'handler_id',
            'store_id',
            'transferred',
            'event',
        ];

        foreach ($attributes as $attribute) {
            if (!empty($this->$attribute)){
                return true;
            }
        }

        return false;
    }
}
