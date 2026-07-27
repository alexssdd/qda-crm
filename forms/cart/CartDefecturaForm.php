<?php

namespace app\forms\cart;

use Yii;
use app\entities\City;
use app\entities\Product;
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
            [['product_id', 'quantity'], 'required'],
            [['product_id', 'city_id'], 'integer'],
            [['product_id'], 'exist', 'targetClass' => Product::class, 'targetAttribute' => 'id'],
            [['city_id'], 'exist', 'targetClass' => City::class, 'targetAttribute' => 'id'],
            [['quantity'], 'integer', 'min' => 1, 'max' => 100000],
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
