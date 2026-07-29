<?php

namespace app\services;

use Yii;
use Throwable;
use DomainException;
use app\forms\ExecutorUpdateForm;
use app\modules\order\models\Executor;
use app\modules\order\models\ExecutorService;

class ExecutorProfileService
{
    public function update(Executor $executor, ExecutorUpdateForm $form): void
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $now = time();
            $executor->location_id = $form->location_id;
            $executor->updated_at = $now;

            if (!$executor->save(false, ['location_id', 'updated_at'])) {
                throw new DomainException('Executor profile was not updated.');
            }

            $existingServices = ExecutorService::find()
                ->where(['executor_id' => $executor->id])
                ->indexBy('type')
                ->all();

            ExecutorService::deleteAll(['executor_id' => $executor->id]);

            $rows = [];
            foreach ($form->service_types as $type) {
                $categories = $existingServices[$type]->categories ?? [];

                if (is_string($categories)) {
                    $categories = json_decode($categories, true);
                }

                $rows[] = [
                    (int) $executor->id,
                    (int) $type,
                    is_array($categories) ? $categories : [],
                    $now,
                ];
            }

            if ($rows !== []) {
                Yii::$app->db->createCommand()
                    ->batchInsert(
                        ExecutorService::tableName(),
                        ['executor_id', 'type', 'categories', 'updated_at'],
                        $rows
                    )
                    ->execute();
            }

            $transaction->commit();
        } catch (Throwable $e) {
            if ($transaction->isActive) {
                $transaction->rollBack();
            }

            throw $e;
        }
    }
}
