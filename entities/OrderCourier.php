<?php

namespace app\entities;

use Yii;
use Exception;
use yii\db\ActiveQuery;
use app\core\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%order_courier}}".
 *
 * @property int $id
 * @property int $order_id
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $lat
 * @property string|null $lng
 * @property array|null $extra_fields
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Order $order
 */
class OrderCourier extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%order_courier}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('order', 'Courier Name'),
            'phone' => Yii::t('order', 'Courier Phone'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getArrivedAt()
    {
        return ArrayHelper::getValue($this->extra_fields, 'arrival_at');
    }
}
