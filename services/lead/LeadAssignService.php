<?php

namespace app\services\lead;

use Exception;
use DomainException;
use app\entities\User;
use app\entities\Lead;

/**
 * Lead assign service
 */
class LeadAssignService
{
    private $_lead;
    private $_user;

    /**
     * LeadBotService constructor.
     * @param Lead $lead
     * @param User $user
     */
    public function __construct(Lead $lead, User $user)
    {
        $this->_lead = $lead;
        $this->_user = $user;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function assign()
    {
        $lead = $this->_lead;
        $user = $this->_user;
        $lead->executor_id = $user->id;

        if ($lead->created_by){
            $lead->handler_id = $lead->created_by;
        } else {
            $lead->handler_id = $user->id;
        }

        if (!$lead->save(false)) {
            throw new DomainException("Lead id: $lead->id, executor set error");
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function setHandler()
    {
        $lead = $this->_lead;

        if ($lead->created_by){
            $lead->handler_id = $lead->created_by;
        } else {
            $lead->handler_id = $this->_user->id;
        }

        if (!$lead->save(false)) {
            throw new DomainException("Lead id: $lead->id, handled set error");
        }
    }
}