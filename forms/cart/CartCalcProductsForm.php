<?php

namespace app\forms\cart;

use Yii;
use app\core\forms\Form;

/**
 * Cart calc products form
 */
class CartCalcProductsForm extends Form
{
    /** Fields */
    public $merchant_id;
    public $city_id;
    public $customer_id;
    public $products;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'city_id'], 'required'],
            [['merchant_id', 'city_id', 'customer_id'], 'integer'],
            [['products'], 'validateProducts'],
        ];
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateProducts($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (empty($this->products)) {
                $this->addError($attribute, Yii::t('cart', 'Empty products'));
            }
        }
    }
}