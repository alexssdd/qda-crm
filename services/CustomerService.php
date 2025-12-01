<?php

namespace app\services;

use Exception;
use DomainException;
use app\entities\Customer;
use app\core\helpers\PhoneHelper;
use app\forms\CustomerUpdateForm;
use app\core\helpers\CustomerHelper;

/**
 * Customer service
 */
class CustomerService
{
    /**
     * @param Customer $model
     * @param CustomerUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(Customer $model, CustomerUpdateForm $form): void
    {
        $model->name = $form->name;
        $model->status = $form->status;
        $model->updated_at = time();

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }

    /**
     * @param $phone
     * @param $name
     * @return Customer|null
     * @throws Exception
     */
    public function findOrCreate($phone, $name): ?Customer
    {
        $phone = PhoneHelper::getCleanNumber($phone);
        $customer = Customer::findOne(['phone' => $phone]);
        if ($customer){
            return $customer;
        }

        $customer = new Customer();
        $customer->name = $name;
        $customer->phone = $phone;
        $customer->status = CustomerHelper::STATUS_ACTIVE;
        $customer->created_at = time();
        $customer->updated_at = time();
        if (!$customer->save()){
            return null;
        }

        return $customer;
    }
}