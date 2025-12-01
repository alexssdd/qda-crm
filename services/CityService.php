<?php

namespace app\services;

use Exception;
use DomainException;
use app\entities\City;
use app\forms\CityCreateForm;
use app\forms\CityUpdateForm;

/**
 * City service
 */
class CityService
{
    /**
     * @param CityCreateForm $form
     * @return City
     * @throws Exception
     */
    public function create(CityCreateForm $form): City
    {
        $model = new City();
        $model->country_id = $form->country_id;
        $model->name = $form->name;
        $model->name_kk = $form->name_kk;
        $model->config = $form->config;
        $model->status = $form->status;

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }

        return $model;
    }

    /**
     * @param City $model
     * @param CityUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(City $model, CityUpdateForm $form): void
    {
        $model->country_id = $form->country_id;
        $model->name = $form->name;
        $model->name_kk = $form->name_kk;
        $model->config = array_merge($model->config, $form->config);
        $model->status = $form->status;

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}