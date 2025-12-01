<?php

namespace app\services\care;

use DomainException;
use app\entities\User;
use app\entities\Care;
use app\entities\CareHistory;

/**
 * Care history service
 */
class CareHistoryService
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
     * @param $statusBefore
     * @param $statusAfter
     * @return void
     */
    public function create($statusBefore, $statusAfter)
    {
        if ($statusBefore == $statusAfter){
            return;
        }

        $model = new CareHistory();
        $model->care_id = $this->_care->id;
        $model->status_before = $statusBefore;
        $model->status_after = $statusAfter;
        $model->created_at = time();
        $model->created_by = $this->_user->id;

        if (!$model->save()){
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}