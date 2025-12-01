<?php

namespace app\forms\cart;

use Yii;
use app\core\forms\Form;

/**
 * Cart stores form
 */
class CartStoresForm extends Form
{
    public $merchant_id;
    public $city_id;
    public $products;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'city_id'], 'required'],
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