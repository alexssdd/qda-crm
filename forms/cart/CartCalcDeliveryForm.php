<?php

namespace app\forms\cart;

use Yii;
use app\entities\City;
use app\entities\Product;
use app\entities\Merchant;
use app\core\forms\Form;

/**
 * Cart calc delivery form
 */
class CartCalcDeliveryForm extends Form
{
    /** Fields */
    public $merchant_id;
    public $city_id;
    public $lat;
    public $lng;
    public $products;

    private $_merchant;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['city_id', 'merchant_id', 'lat', 'lng'], 'required'],
            [['city_id', 'merchant_id'], 'integer'],
            [['city_id'], 'exist', 'targetClass' => City::class, 'targetAttribute' => 'id'],
            [['lat'], 'number', 'min' => -90, 'max' => 90],
            [['lng'], 'number', 'min' => -180, 'max' => 180],
            [['products'], 'validateProducts'],
            ['merchant_id', 'validateMerchant']
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
    public function validateProducts($attribute, $params): void
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

            $productId = (int) $item['id'];
            if (isset($productIds[$productId])) {
                $this->addError($attribute, 'Товар указан несколько раз');
                return;
            }
            $productIds[$productId] = $item['sku'];
        }

        $products = Product::find()
            ->select(['id', 'sku'])
            ->andWhere([
                'id' => array_keys($productIds),
                'merchant_id' => $this->merchant_id,
            ])
            ->indexBy('id')
            ->all();

        foreach ($productIds as $productId => $sku) {
            if (!isset($products[$productId]) || (string) $products[$productId]->sku !== $sku) {
                $this->addError($attribute, 'Товар не соответствует выбранному магазину');
                return;
            }
        }
    }

    public function validateMerchant($attribute): void
    {
        if (!$this->hasErrors() && !$this->getMerchant()) {
            $this->addError($attribute, 'Merchant not found');
        }
    }

    public function getMerchant(): ?Merchant
    {
        if ($this->_merchant === null) {
            $this->_merchant = Merchant::findOne($this->merchant_id);
        }
        return $this->_merchant;
    }
}
