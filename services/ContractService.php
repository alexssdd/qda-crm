<?php

namespace app\services;

use DomainException;
use app\entities\Contract;
use app\forms\ContractCreateForm;
use app\forms\ContractUpdateForm;

/**
 * Contract service
 */
class ContractService
{
    /**
     * @param ContractCreateForm $form
     * @return Contract
     */
    public function create(ContractCreateForm $form): Contract
    {
        $model = new Contract();
        $model->customer_id = $form->customer_id;
        $model->merchant_id = $form->merchant_id;
        $model->number = $form->number;
        $model->status = $form->status;
        $model->created_at = time();
        $model->updated_at = time();

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }

        return $model;
    }

    /**
     * @param Contract $model
     * @param ContractUpdateForm $form
     * @return void
     */
    public function update(Contract $model, ContractUpdateForm $form)
    {
        $model->number = $form->number;
        $model->status = $form->status;
        $model->updated_at = time();

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}