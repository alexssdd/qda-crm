<?php

namespace app\forms\cart;

use Yii;
use app\core\forms\Form;

/**
 * Cart defectura form
 */
class CartDefecturaForm extends Form
{
    public $product_id;
    public $product_name;
    public $quantity;
    public $city_id;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['product_id'], 'required'],
            [['quantity', 'city_id'], 'safe'],
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
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'product_name' => Yii::t('app', 'Product ID'),
            'quantity' => Yii::t('app', 'Quantity'),
            'city_id' => Yii::t('app', 'City ID'),
        ];
    }
}