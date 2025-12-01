<?php

namespace app\services\lead;

use Exception;
use DomainException;
use app\entities\User;
use app\entities\Lead;
use yii\db\ActiveRecord;
use app\entities\LeadEvent;
use app\entities\LeadHistory;

/**
 * Lead event service
 */
class LeadEventService
{
    private $_lead;
    private $_user;

    /**
     * @param Lead $lead
     * @param User $user
     */
    public function __construct(Lead $lead, User $user)
    {
        $this->_lead = $lead;
        $this->_user = $user;
    }

    /**
     * @param string $message
     * @param $type
     * @param array $data
     * @return LeadEvent|null
     * @throws Exception
     */
    public function create(string $message = '', $type = null, array $data = []): ?LeadEvent
    {
        $history = $this->getLastHistory();
        if (!$history){
            return null;
        }

        $model = new LeadEvent();
        $model->lead_id = $this->_lead->id;
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
     * @return LeadHistory|ActiveRecord|null
     */
    protected function getLastHistory(): ?LeadHistory
    {
        return LeadHistory::find()
            ->andWhere(['lead_id' => $this->_lead->id])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
    }
}