<?php

namespace app\entities;

use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%lead_history}}".
 *
 * @property int $id
 * @property int $lead_id
 * @property int|null $type
 * @property string|null $message
 * @property int|null $status_before
 * @property int|null $status_after
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property LeadEvent[] $leadEvents
 * @property Lead $lead
 * @property User $createdBy
 */
class LeadHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%lead_history}}';
    }

    /**
     * Gets query for [[LeadEvents]].
     *
     * @return ActiveQuery
     */
    public function getLeadEvents(): ActiveQuery
    {
        return $this->hasMany(LeadEvent::class, ['history_id' => 'id']);
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
}
