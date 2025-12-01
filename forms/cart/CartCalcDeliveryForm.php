<?php

namespace app\forms\cart;

use app\entities\Merchant;
use Yii;
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
            [['city_id', 'merchant_id'], 'required'],
            [['city_id', 'merchant_id'], 'integer'],
            [['lat', 'lng', 'products'], 'validateProducts'],
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
        if (!$this->hasErrors()) {
            if (empty($this->products)) {
                $this->addError($attribute, Yii::t('cart', 'Empty products'));
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