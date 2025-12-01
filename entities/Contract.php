<?php

namespace app\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%contract}}".
 *
 * @property int $id
 * @property int $customer_id
 * @property int|null $merchant_id
 * @property string|null $number
 * @property string|null $config
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Customer $customer
 * @property Merchant $merchant
 */
class Contract extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%contract}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'merchant_id' => Yii::t('app', 'Merchant ID'),
            'number' => Yii::t('app', 'Number'),
            'config' => Yii::t('app', 'Config'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Merchant]].
     *
     * @return ActiveQuery
     */
    public function getMerchant(): ActiveQuery
    {
        return $this->hasOne(Merchant::class, ['id' => 'merchant_id']);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->number;
    }
}
