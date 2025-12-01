<?php

namespace app\services;

use DomainException;
use app\entities\Country;
use app\forms\CountryUpdateForm;

/**
 * Country service
 */
class CountryService
{
    /**
     * @param Country $model
     * @param CountryUpdateForm $form
     * @return void
     */
    public function update(Country $model, CountryUpdateForm $form): void
    {
        $model->name = $form->name;
        $model->status = $form->status;

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}