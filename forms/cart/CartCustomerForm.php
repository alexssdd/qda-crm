<?php

namespace app\forms\cart;

use app\core\forms\Form;

/**
 * Cart customer form
 */
class CartCustomerForm extends Form
{
    public $city_id;
    public $phone;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['phone'], 'required'],
            [['city_id'], 'integer']
        ];
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }
}