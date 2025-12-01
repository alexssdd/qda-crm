<?php

namespace app\entities;

use Yii;
use Exception;
use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%report}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $type
 * @property string|null $date_from
 * @property string|null $date_to
 * @property string|null $comment
 * @property array|null $params
 * @property int|null $status
 * @property int|null $created_at
 *
 * @property User $user
 */
class Report extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%report}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'type' => Yii::t('app', 'Type'),
            'date_from' => Yii::t('app', 'Date From'),
            'date_to' => Yii::t('app', 'Date To'),
            'comment' => Yii::t('app', 'Comment'),
            'params' => Yii::t('app', 'Params'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return array|null
     */
    public function getParams(): ?array
    {
        try {
            return $this->params;
        } catch (Exception $e){
            return [];
        }
    }
}
