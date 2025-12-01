<?php

namespace app\widgets;

use Exception;
use yii\base\Widget;
use app\entities\Order;
use app\entities\Customer;
use app\forms\AddressSelectForm;
use app\core\helpers\PhoneHelper;

/**
 * Class AddressSelectWidget
 * @package app\widgets
 */
class AddressSelectWidget extends Widget
{
    /** @var Order */
    public $order;

    /** @var array */
    public $action;

    /** @var array */
    public $attributes;

    /** @var string */
    public $doneCallback = 'order';

    /**
     * @return string
     * @throws Exception
     */
    public function run(): string
    {
        $model = new AddressSelectForm($this->order);

        // Set attributes
        if ($this->attributes){
            $model->setAttributes($this->attributes);
        }

        // Customer
        if (!$model->customer_id && $model->phone){
            $customer = Customer::findOne(['phone' => PhoneHelper::getCleanNumber($model->phone)]);
            $model->customer_id = $customer ? $customer->id : null;
        }

        return $this->render('address_select', [
            'model' => $model,
            'action' => $this->action,
            'doneCallback' => $this->doneCallback
        ]);
    }

}