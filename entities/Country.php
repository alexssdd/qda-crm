<?php

namespace app\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%country}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $iso
 * @property array|null $config
 * @property int|null $status
 *
 * @property City[] $cities
 */
class Country extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%country}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'iso' => Yii::t('app', 'Iso'),
            'config' => Yii::t('app', 'Config'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[Cities]].
     *
     * @return ActiveQuery
     */
    public function getCities(): ActiveQuery
    {
        return $this->hasMany(City::class, ['country_id' => 'id']);
    }
}
