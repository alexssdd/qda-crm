<?php

namespace app\services;

use DomainException;
use app\entities\PriceType;
use app\forms\PriceTypeCreateForm;
use app\forms\PriceTypeUpdateForm;

/**
 * Price type service
 */
class PriceTypeService
{
    /**
     * @param PriceTypeCreateForm $form
     * @return PriceType
     */
    public function create(PriceTypeCreateForm $form): PriceType
    {
        $model = new PriceType();
        $model->code = $form->code;
        $model->name = $form->name;
        $model->status = $form->status;

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }

        return $model;
    }

    /**
     * @param PriceType $model
     * @param PriceTypeUpdateForm $form
     * @return void
     */
    public function update(PriceType $model, PriceTypeUpdateForm $form)
    {
        $model->code = $form->code;
        $model->name = $form->name;
        $model->status = $form->status;

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}