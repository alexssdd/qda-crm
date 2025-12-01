<?php

namespace app\entities;

use Exception;
use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%order_payment}}".
 *
 * @property int $id
 * @property int $order_id
 * @property string $number
 * @property int $type
 * @property int $provider
 * @property string|null $provider_id
 * @property float|null $provider_cost
 * @property string|null $config
 * @property int $status
 * @property int|null $callback_at
 * @property int $created_at
 *
 * @property Order $order
 */
class OrderPayment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%order_payment}}';
    }

    /**
     * @param $insert
     * @param $changedAttributes
     * @return void
     * @throws Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$this->number) {
            $this->number = random_int(1000, 1999) . sprintf("%06d", $this->id);
            $this->updateAttributes(['number']);
        }
    }

    /**
     * Gets query for [[Order]].
     *
     * @return ActiveQuery
     */
    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
}
