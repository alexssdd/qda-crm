<?php

namespace app\entities;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%care_event}}".
 *
 * @property int $id
 * @property int $care_id
 * @property int $history_id
 * @property int|null $type
 * @property string|null $message
 * @property string|null $data
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property Care $care
 * @property User $createdBy
 * @property CareHistory $history
 */
class CareEvent extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%care_event}}';
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

    /**
     * Gets query for [[History]].
     *
     * @return ActiveQuery
     */
    public function getHistory(): ActiveQuery
    {
        return $this->hasOne(CareHistory::class, ['id' => 'history_id']);
    }
}
