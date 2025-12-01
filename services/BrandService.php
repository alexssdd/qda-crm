<?php

namespace app\services;

use Exception;
use DomainException;
use app\entities\Brand;
use app\forms\BrandCreateForm;
use app\forms\BrandUpdateForm;

/**
 * Brand service
 */
class BrandService
{
    /**
     * @param BrandCreateForm $form
     * @return Brand
     * @throws Exception
     */
    public function create(BrandCreateForm $form): Brand
    {
        $model = new Brand();
        $model->name = $form->name;
        $model->status = $form->status;
        $model->config = [
            'merchant_id' => $form->merchant_id
        ];
        $model->created_at = time();
        $model->updated_at = time();

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }

        return $model;
    }

    /**
     * @param Brand $model
     * @param BrandUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(Brand $model, BrandUpdateForm $form)
    {
        $model->name = $form->name;
        $model->status = $form->status;
        $model->config = [
            'merchant_id' => $form->merchant_id
        ];
        $model->updated_at = time();

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}