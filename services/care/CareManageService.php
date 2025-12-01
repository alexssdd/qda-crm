<?php

namespace app\services\care;

use Exception;
use DomainException;
use app\entities\Care;
use app\core\helpers\UserHelper;
use app\core\helpers\CareHelper;
use app\core\helpers\PhoneHelper;
use app\forms\care\CareUpdateForm;
use app\core\helpers\ZvonobotHelper;
use app\forms\care\CareSolutionForm;

/**
 * Care manage service
 */
class CareManageService
{
    private $_care;

    /**
     * @param Care $care
     */
    public function __construct(Care $care)
    {
        $this->_care = $care;
    }

    /**
     * @param CareUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(CareUpdateForm $form): void
    {
        $care = $this->_care;

        $care->order_number = $form->order_number;
        $care->category = $form->category;
        $care->complaint_reason = $form->complaint_reason;
        $care->store_number = $form->store_number;
        $care->delivery_late = $form->delivery_late;
        $care->solution_measures = $form->solution_measures;
        $care->complaint_personal = $form->complaint_personal;
        $care->complaint_validity = $form->complaint_validity;
        $care->compensation = $form->compensation;
        $care->final_status = $form->final_status;
        $care->status = $form->status;

        // Check if finished
        if (CareHelper::isFinished($care->status)){
            $this->checkFinished();
        }

        // Set handler
        if (!$care->handler_id && $care->status == CareHelper::STATUS_ACCEPTED){
            $care->handler_id = UserHelper::getIdentity()->id;
        }

        // Set completed at
        if (CareHelper::isFinished($care->status) && !$care->completed_at){
            $care->completed_at = time();
        }

        // Save
        if (!$care->save(false)) {
            throw new DomainException("Care id: $care->id, save error");
        }

        // Enable callback
        $this->enableCallback();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function enableCallback(): void
    {
        $care = $this->_care;

        // Check status
        if (!in_array($care->status, [CareHelper::STATUS_FINISHED_GOOD, CareHelper::STATUS_FINISHED_BAD])){
            return;
        }

        // Check callback status
        if ($care->callback_status !== null){
            return;
        }

        // Check phone
        $phone = PhoneHelper::getCleanNumber($care->phone);
        if (in_array($phone, ZvonobotHelper::EXCLUDED_PHONES)){
            return;
        }

        // Check rating
        if ($care->rating > 3){
            return;
        }

        $care->callback_status = CareHelper::CALLBACK_STATUS_ATTEMPT_1;
        if (!$care->save()){
            throw new DomainException($care->getErrorSummary(true)[0]);
        }
    }

    /**
     * @param CareSolutionForm $form
     * @return void
     * @throws Exception
     */
    public function solution(CareSolutionForm $form): void
    {
        $care = $this->_care;
        $care->solution_text = $form->text;

        if (!$care->save(false)) {
            throw new DomainException("Care id: $care->id, solution error");
        }
    }

    /**
     * @return void
     */
    public function checkFinished(): void
    {
        $care = $this->_care;

        if ($care->type == CareHelper::TYPE_POSITIVE){
            return;
        }
        if (!$care->solution_text){
            throw new DomainException('Перед завершением необходимо подробно описать решение');
        }
    }
}