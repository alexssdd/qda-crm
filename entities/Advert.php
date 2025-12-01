<?php

namespace app\entities;

use Yii;
use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%advert}}".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $text
 * @property string|null $config
 * @property int|null $status
 * @property int|null $begin_at
 * @property int|null $end_at
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property AdvertUser[] $advertUsers
 */
class Advert extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%advert}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'text' => Yii::t('app', 'Text'),
            'status' => Yii::t('app', 'Status'),
            'begin_at' => Yii::t('app', 'Begin At'),
            'end_at' => Yii::t('app', 'End At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAdvertUsers(): ActiveQuery
    {
        return $this->hasMany(AdvertUser::class, ['advert_id' => 'id']);
    }
}
