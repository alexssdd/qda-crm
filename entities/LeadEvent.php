<?php

namespace app\entities;

use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%lead_event}}".
 *
 * @property int $id
 * @property int $lead_id
 * @property int $history_id
 * @property int|null $type
 * @property string|null $message
 * @property string|null $data
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property Lead $lead
 * @property User $createdBy
 * @property LeadHistory $history
 */
class LeadEvent extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%lead_event}}';
    }

    /**
     * Gets query for [[Lead]].
     *
     * @return ActiveQuery
     */
    public function getLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'lead_id']);
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
        return $this->hasOne(LeadHistory::class, ['id' => 'history_id']);
    }
}
