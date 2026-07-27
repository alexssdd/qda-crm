<?php

namespace app\forms\cart;

use Yii;
use app\entities\City;
use app\entities\Product;
use app\entities\Merchant;
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
            [['merchant_id', 'city_id'], 'integer'],
            [['merchant_id'], 'exist', 'targetClass' => Merchant::class, 'targetAttribute' => 'id'],
            [['city_id'], 'exist', 'targetClass' => City::class, 'targetAttribute' => 'id'],
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

        $skus = [];
        foreach ($this->products as $item) {
            if (
                !is_array($item)
                || !is_string($item['sku'] ?? null)
                || $item['sku'] === ''
                || mb_strlen($item['sku']) > 100
                || !isset($item['quantity'])
                || !is_numeric($item['quantity'])
                || (float) $item['quantity'] <= 0
                || (float) $item['quantity'] > 100000
            ) {
                $this->addError($attribute, 'Некорректные данные товара');
                return;
            }

            if (isset($skus[$item['sku']])) {
                $this->addError($attribute, 'Товар указан несколько раз');
                return;
            }
            $skus[$item['sku']] = true;
        }

        $productSkus = Product::find()
            ->select('sku')
            ->andWhere([
                'merchant_id' => $this->merchant_id,
                'sku' => array_keys($skus),
            ])
            ->column();

        if (count(array_unique($productSkus)) !== count($skus)) {
            $this->addError($attribute, 'Товар не соответствует выбранному магазину');
        }
    }
}
