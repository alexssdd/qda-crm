<?php

namespace app\modules\order\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $executor_id
 * @property int $type
 * @property array|string|null $categories
 * @property int $updated_at
 *
 * @property-read Executor $executor
 */
class ExecutorService extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%executor_service}}';
    }

    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(Executor::class, ['id' => 'executor_id']);
    }
}
