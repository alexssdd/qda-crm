<?php

namespace app\services;

use DomainException;
use app\forms\CountryUpdateForm;
use app\modules\location\models\Country;

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
        $model->updated_at = time();

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}