<?php

namespace app\services\care;

use DomainException;
use app\entities\User;
use app\entities\Care;
use yii\db\ActiveRecord;
use app\entities\CareEvent;
use app\entities\CareHistory;

/**
 * Care event service
 */
class CareEventService
{
    private $_care;
    private $_user;

    /**
     * @param Care $care
     * @param User $user
     */
    public function __construct(Care $care, User $user)
    {
        $this->_care = $care;
        $this->_user = $user;
    }

    /**
     * @param string $message
     * @param $type
     * @param array $data
     * @return CareEvent|null
     */
    public function create(string $message = '', $type = null, array $data = []): ?CareEvent
    {
        $history = $this->getLastHistory();
        if (!$history){
            return null;
        }

        $model = new CareEvent();
        $model->care_id = $this->_care->id;
        $model->history_id = $history->id;
        $model->type = $type;
        $model->message = $message;
        $model->data = $data;
        $model->created_at = time();
        $model->created_by = $this->_user->id;

        if (!$model->save()){
            throw new DomainException($model->getErrorSummary(true)[0]);
        }

        return $model;
    }

    /**
     * @return CareHistory|ActiveRecord|null
     */
    protected function getLastHistory(): ?CareHistory
    {
        return CareHistory::find()
            ->andWhere(['care_id' => $this->_care->id])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
    }
}