<?php

namespace app\entities;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%care_history}}".
 *
 * @property int $id
 * @property int $care_id
 * @property int|null $type
 * @property string|null $message
 * @property int|null $status_before
 * @property int|null $status_after
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property CareEvent[] $careEvents
 * @property Care $care
 * @property User $createdBy
 */
class CareHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%care_history}}';
    }

    /**
     * Gets query for [[CareEvents]].
     *
     * @return ActiveQuery
     */
    public function getCareEvents(): ActiveQuery
    {
        return $this->hasMany(CareEvent::class, ['history_id' => 'id']);
    }

    /**
     * Gets query for [[Care]].
     *
     * @return ActiveQuery
     */
    public function getCare(): ActiveQuery
    {
        return $this->hasOne(Care::class, ['id' => 'care_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return ActiveQuery
     */
    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
}
