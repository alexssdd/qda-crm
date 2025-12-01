<?php

namespace app\services\lead;

use Exception;
use DomainException;
use app\entities\User;
use app\entities\Lead;
use app\entities\LeadHistory;

/**
 * Lead history service
 */
class LeadHistoryService
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
     * @param $statusBefore
     * @param $statusAfter
     * @return void
     * @throws Exception
     */
    public function create($statusBefore, $statusAfter)
    {
        if ($statusBefore == $statusAfter){
            return;
        }

        $model = new LeadHistory();
        $model->lead_id = $this->_lead->id;
        $model->status_before = $statusBefore;
        $model->status_after = $statusAfter;
        $model->created_at = time();
        $model->created_by = $this->_user->id;

        if (!$model->save()){
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}