<?php

namespace app\forms\cart;

use Yii;
use app\entities\City;
use app\entities\Product;
use app\entities\Customer;
use app\entities\Merchant;
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
            [['merchant_id'], 'exist', 'targetClass' => Merchant::class, 'targetAttribute' => 'id'],
            [['city_id'], 'exist', 'targetClass' => City::class, 'targetAttribute' => 'id'],
            [['customer_id'], 'exist', 'targetClass' => Customer::class, 'targetAttribute' => 'id'],
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
        if ($this->hasErrors()) {
            return;
        }

        if (!is_array($this->products) || !$this->products || count($this->products) > 100) {
            $this->addError($attribute, Yii::t('cart', 'Empty products'));
            return;
        }

        $productIds = [];
        foreach ($this->products as $item) {
            if (
                !is_array($item)
                || filter_var($item['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false
                || !isset($item['quantity'])
                || !is_numeric($item['quantity'])
                || (float) $item['quantity'] <= 0
                || (float) $item['quantity'] > 100000
            ) {
                $this->addError($attribute, 'Некорректные данные товара');
                return;
            }

            $productId = (int) $item['id'];
            if (isset($productIds[$productId])) {
                $this->addError($attribute, 'Товар указан несколько раз');
                return;
            }
            $productIds[$productId] = true;
        }

        $productCount = Product::find()
            ->andWhere([
                'id' => array_keys($productIds),
                'merchant_id' => $this->merchant_id,
            ])
            ->count();

        if ((int) $productCount !== count($productIds)) {
            $this->addError($attribute, 'Товар не соответствует выбранному магазину');
        }
    }
}
