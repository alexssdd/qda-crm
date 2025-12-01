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
 * @property string $name
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $ref
 * @property int|null $type
 * @property string|null $iin
 * @property array|null $config
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * 
 * @property Customer $parent
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
            'name' => Yii::t('app', 'Name'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'Email'),
            'ref' => Yii::t('app', 'Ref'),
            'type' => Yii::t('app', 'Type'),
            'iin' => Yii::t('app', 'Iin'),
            'config' => Yii::t('app', 'Config'),
            'status' => Yii::t('app', 'Status'),
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
