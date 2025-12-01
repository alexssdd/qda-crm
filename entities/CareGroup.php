<?php

namespace app\entities;

use Yii;
use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%care_group}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $config
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Care[] $cares
 */
class CareGroup extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%care_group}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'config' => Yii::t('app', 'Config'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Cares]].
     *
     * @return ActiveQuery
     */
    public function getCares(): ActiveQuery
    {
        return $this->hasMany(Care::class, ['group_id' => 'id']);
    }
}
