<?php

namespace app\services;

use DomainException;
use app\entities\Address;
use app\forms\AddressCreateForm;
use app\forms\AddressUpdateForm;

/**
 * Address service
 */
class AddressService
{
    /**
     * @param AddressCreateForm $form
     * @return Address
     */
    public function create(AddressCreateForm $form): Address
    {
        $model = new Address();
        $model->customer_id = $form->customer_id;
        $model->city_id = $form->city_id;
        $model->address = $form->address;
        $model->lat = $form->lat;
        $model->lng = $form->lng;
        $model->status = $form->status;
        $model->created_at = time();
        $model->updated_at = time();

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }

        return $model;
    }

    /**
     * @param Address $model
     * @param AddressUpdateForm $form
     * @return void
     */
    public function update(Address $model, AddressUpdateForm $form)
    {
        $model->city_id = $form->city_id;
        $model->address = $form->address;
        $model->lat = $form->lat;
        $model->lng = $form->lng;
        $model->status = $form->status;
        $model->updated_at = time();

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}