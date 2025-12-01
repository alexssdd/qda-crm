<?php

namespace app\entities;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%jivosite}}".
 *
 * @property int $id
 * @property int $chat_id
 * @property int|null $client_id
 * @property string $widget_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $finished_at
 *
 * @property JivositeEvent[] $jivositeEvents
 */
class Jivosite extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%jivosite}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getJivositeEvents(): ActiveQuery
    {
        return $this->hasMany(JivositeEvent::class, ['chat_id' => 'id']);
    }
}
