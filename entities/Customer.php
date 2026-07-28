<?php

namespace app\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%customer}}".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int|null $source_id
 * @property string|null $country_code
 * @property int $source_synced_at
 * @property string $name
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $ref
 * @property int|null $type
 * @property string|null $iin
 * @property array|null $config
 * @property int|null $status
 * @property int|null $registered_at
 * @property int $orders_created
 * @property int $orders_completed
 * @property int $orders_canceled
 * @property int|null $last_order_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Customer $parent
 * @property \app\modules\location\models\Country|null $country
 * @property Address[] $addresses
 * @property Contract[] $contracts
 */
class Customer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%customer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'source_id' => Yii::t('app', 'Source ID'),
            'country_code' => Yii::t('app', 'Country'),
            'source_synced_at' => 'Source Synced At',
            'name' => Yii::t('app', 'Name'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'Email'),
            'ref' => Yii::t('app', 'Ref'),
            'type' => Yii::t('app', 'Type'),
            'iin' => Yii::t('app', 'Iin'),
            'config' => Yii::t('app', 'Config'),
            'status' => Yii::t('app', 'Status'),
            'registered_at' => Yii::t('app', 'Registered At'),
            'orders_created' => Yii::t('app', 'Orders Created'),
            'orders_completed' => Yii::t('app', 'Orders Completed'),
            'orders_canceled' => Yii::t('app', 'Orders Canceled'),
            'last_order_at' => Yii::t('app', 'Last Order At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getParent(): ActiveQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'parent_id']);
    }

    public function getCountry(): ActiveQuery
    {
        return $this->hasOne(
            \app\modules\location\models\Country::class,
            ['code' => 'country_code']
        );
    }

    /**
     * Gets query for [[Addresses]].
     *
     * @return ActiveQuery
     */
    public function getAddresses(): ActiveQuery
    {
        return $this->hasMany(Address::class, ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Contracts]].
     *
     * @return ActiveQuery
     */
    public function getContracts(): ActiveQuery
    {
        return $this->hasMany(Contract::class, ['customer_id' => 'id']);
    }
}
