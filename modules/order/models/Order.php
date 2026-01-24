<?php

namespace app\modules\order\models;

use Exception;
use DomainException;
use yii\db\ActiveQuery;
use app\core\ActiveRecord;
use app\services\LogService;
use yii\helpers\ArrayHelper;
use app\core\helpers\LogHelper;
use app\core\helpers\OrderHelper;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property int $id
 * @property int|null $merchant_id
 * @property int|null $handler_id
 * @property int|null $city_id
 * @property int|null $customer_id
 * @property int|null $store_id
 * @property int|null $delivery_method
 * @property int|null $payment_method
 * @property int|null $executor_id
 * @property int|null $created_by
 * @property int|null $type
 * @property int|null $channel
 * @property int|null $number
 * @property string|null $vendor_id
 * @property string|null $vendor_number
 * @property string|null $external_number
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $address
 * @property float|null $lat
 * @property float|null $lng
 * @property float|null $amount
 * @property int|null $delivery_cost
 * @property string|null $comment
 * @property array|null $extra_fields
 * @property int $created_at
 * @property int|null $completed_at
 * @property int $status
 * @property int $account_id
 *
 * @property User $handler
 * @property Customer $customer
 * @property Store $store
 * @property User $executor
 * @property User $createdBy
 * @property OrderProduct[] $products
 * @property OrderStore[] $stores
 * @property OrderHistory[] $histories
 * @property OrderHistory $lastAcceptedHistory
 * @property OrderHistory $lastShippedHistory
 * @property OrderEvent[] $events
 * @property OrderPayment $paymentPaid
 * @property Merchant $merchant
 * @property OrderCourier $courier
 * @property OrderReason $cancelReason
 */
class Order extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%order}}';
    }

    /**
     * @return void
     * @throws Exception
     */
    public function generateNumber(): void
    {
        $target = LogHelper::TARGET_ORDER_GENERATE_NUMBER;
        $attempts = 0;
        $maxAttempts = 3;

        try {
            do {
                if ($attempts >= $maxAttempts) {
                    throw new DomainException('Не удалось сгенерировать уникальный номер');
                }

                $number = ($this->id * 88) + random_int(10000, 99999);

                if (!static::find()
                    ->andWhere(['number' => $number])
                    ->andWhere(['!=', 'id', $this->id])
                    ->exists()
                ) {
                    break;
                }

                $attempts++;
            } while (true);

            $this->number = $number;

            if (!$this->save(false)) {
                throw new DomainException("Unable save after generate number");
            }
        } catch (DomainException $e) {
            LogService::error($target, ['id' => $this->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * @return ActiveQuery
     */
    public function getHandler(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'handler_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStore(): ActiveQuery
    {
        return $this->hasOne(Store::class, ['id' => 'store_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(OrderProduct::class, ['order_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStores(): ActiveQuery
    {
        return $this->hasMany(OrderStore::class, ['order_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHistories(): ActiveQuery
    {
        return $this->hasMany(OrderHistory::class, ['order_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLastAcceptedHistory(): ActiveQuery
    {
        return $this->hasOne(OrderHistory::class, ['order_id' => 'id'])
            ->andOnCondition(['status_after' => OrderHelper::STATUS_ACCEPTED])
            ->orderBy([OrderHistory::tableName() . '.id' => SORT_DESC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getLastShippedHistory(): ActiveQuery
    {
        return $this->hasOne(OrderHistory::class, ['order_id' => 'id'])
            ->andOnCondition(['status_after' => OrderHelper::STATUS_SHIPPED])
            ->orderBy([OrderHistory::tableName() . '.id' => SORT_DESC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getEvents(): ActiveQuery
    {
        return $this->hasMany(OrderEvent::class, ['order_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPaymentPaid(): ActiveQuery
    {
        return $this->hasOne(OrderPayment::class, ['order_id' => 'id'])
            ->andWhere([
                'status' => PaymentHelper::STATUS_SUCCESS
            ]);
    }

    /**
     * @return ActiveQuery
     */
    public function getMerchant(): ActiveQuery
    {
        return $this->hasOne(Merchant::class, ['id' => 'merchant_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCourier(): ActiveQuery
    {
        return $this->hasOne(OrderCourier::class, ['order_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCancelReason(): ActiveQuery
    {
        return $this->hasOne(OrderReason::class, ['order_id' => 'id'])
            ->andOnCondition(['action' => OrderReasonHelper::ACTION_CANCEL]);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isPending(): bool
    {
        return ArrayHelper::getValue($this->extra_fields, 'is_pending', false);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isDelay(): bool
    {
        return ArrayHelper::getValue($this->extra_fields, 'is_delay', false);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getHouse()
    {
        return ArrayHelper::getValue($this->extra_fields, 'house');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getApartment()
    {
        return ArrayHelper::getValue($this->extra_fields, 'apartment');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getIntercom()
    {
        return ArrayHelper::getValue($this->extra_fields, 'intercom');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getEntrance()
    {
        return ArrayHelper::getValue($this->extra_fields, 'entrance');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getFloor()
    {
        return ArrayHelper::getValue($this->extra_fields, 'floor');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getAddressType()
    {
        return ArrayHelper::getValue($this->extra_fields, 'address_type');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getAddressTitle()
    {
        return ArrayHelper::getValue($this->extra_fields, 'address_title');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getLeadId()
    {
        return ArrayHelper::getValue($this->extra_fields, 'lead_id');
    }
}
