<?php

namespace app\services\lead;

use Exception;
use DomainException;
use app\entities\Lead;
use app\forms\lead\LeadUpdateForm;
use app\forms\lead\LeadTransferForm;

/**
 * Lead manage service
 */
class LeadManageService
{
    private $_lead;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->_lead = $lead;
    }

    /**
     * @param LeadUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(LeadUpdateForm $form): void
    {
        $lead = $this->_lead;
        $lead->city_id = $form->city_id;
        $lead->status = $form->status;

        if (!$lead->save(false)) {
            throw new DomainException("Lead id: $lead->id, save error");
        }
    }

    /**
     * @param LeadTransferForm $form
     * @return void
     * @throws Exception
     */
    public function transfer(LeadTransferForm $form)
    {
        $lead = $this->_lead;
        $lead->executor_id = $form->executor_id;

        if (!$lead->save(false)) {
            throw new DomainException("Lead id: $lead->id, transfer error");
        }
    }
}