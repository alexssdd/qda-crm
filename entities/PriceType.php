<?php

namespace app\entities;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%price_type}}".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $config
 * @property int|null $status
 */
class PriceType extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%price_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'config' => Yii::t('app', 'Config'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
