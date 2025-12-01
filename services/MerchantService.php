<?php

namespace app\services;

use Exception;
use DomainException;
use app\entities\Merchant;
use app\forms\MerchantUpdateForm;
use app\core\helpers\MerchantHelper;

/**
 * Merchant service
 */
class MerchantService
{
    private $_merchant;

    public function __construct(Merchant $merchant)
    {
        $this->_merchant = $merchant;
    }

    /**
     * @param $name
     * @param $code
     * @return void
     * @throws Exception
     */
    public function create($name, $code)
    {
        $model = new Merchant();
        $model->name = $name;
        $model->code = $code;
        $model->status = MerchantHelper::STATUS_ACTIVE;

        if (!$model->save(false)) {
            throw new DomainException($model->getErrorMessage());
        }
    }

    /**
     * @param Merchant $model
     * @param MerchantUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(MerchantUpdateForm $form)
    {
        $model = $this->_merchant;
        $model->name = $form->name;
        $model->status = $form->status;

        if (!$model->save()) {
            throw new DomainException($model->getErrorMessage());
        }
    }
}