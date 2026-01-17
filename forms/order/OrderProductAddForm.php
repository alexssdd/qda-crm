<?php

namespace app\forms\order;

use Yii;
use Exception;
use app\entities\Order;
use app\core\forms\Form;
use app\entities\Product;
use app\core\rules\OrderRules;
use app\services\ProductService;
use app\core\helpers\OrderHelper;

/**
 * Order product add form
 */
class OrderProductAddForm extends Form
{
    public $product_id;
    public $quantity;

    private $_order;
    private $_product;
    private $_price;

    /**
     * @param Order $order
     * @param array $config
     */
    public function __construct(Order $order, array $config = [])
    {
        $this->_order = $order;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['product_id', 'quantity'], 'integer'],
            [['product_id'], 'required'],
            [['product_id'], 'validateProduct'],
            [['product_id'], 'validateChannel'],
            [['product_id'], 'validateStatus'],
            [['product_id'], 'validatePaymentTypes'],
            [['product_id'], 'validatePaymentLimit']
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateProduct($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $product = $this->getProduct();
            if (!$product) {
                $this->addError($attribute, Yii::t('order', 'Product not found'));
            }
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateChannel($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            if (OrderRules::canAddProduct($this->_order->channel)) {
                return;
            }

            $this->addError($attribute, Yii::t('order', 'This channel is not allowed to add products'));
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validateStatus($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $allowedStatuses = [OrderHelper::STATUS_NEW, OrderHelper::STATUS_ACCEPTED];
            if (!in_array($this->_order->status, $allowedStatuses)) {
                $this->addError($attribute, Yii::t('order', 'Order cannot be edited in this status.'));
            }
        }
    }

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        if ($this->_product === null) {
            $this->_product = Product::findOne($this->product_id);
        }
        return $this->_product;
    }
}