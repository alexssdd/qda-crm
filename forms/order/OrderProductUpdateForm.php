<?php

namespace app\forms\order;

use Yii;
use Exception;
use app\entities\Order;
use app\core\forms\Form;
use yii\helpers\ArrayHelper;
use app\core\rules\OrderRules;
use app\core\helpers\OrderHelper;
use app\core\helpers\OrderStoreHelper;

/**
 * Order product update form
 */
class OrderProductUpdateForm extends Form
{
    public $products;

    private $_order;

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
     * @return array[]
     */
    public function rules(): array
    {
        return [
            ['products', 'required'],
            ['products', 'validateAssembly'],
            ['products', 'validateCanUpdate'],
            ['products', 'validateCanDelete'],
            ['products', 'validateStatus'],
            ['products', 'validateQuantityMax'],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validateAssembly($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $order = $this->_order;
            foreach ($order->products as $orderProduct) {
                foreach ($orderProduct->orderStoreProducts as $orderStoreProduct) {
                    if ($orderStoreProduct->orderStore->status == OrderStoreHelper::STATUS_CANCELED){
                        continue;
                    }
                    if ($orderStoreProduct->quantity > 0) {
                        $this->addError($attribute, Yii::t('order', 'You cant edit while assembled, you must first delete the build'));
                        break;
                    }
                }
            }
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
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validateCanUpdate($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            if (OrderRules::canUpdateProduct($this->_order->channel)) {
                return;
            }

            $this->addError($attribute, Yii::t('order', 'This channel is not allowed to edit products'));
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validateCanDelete($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            if (OrderRules::canDeleteProduct($this->_order->channel)) {
                return;
            }

            $this->addError($attribute, Yii::t('order', 'This channel is not allowed to delete products'));
        }
    }

    /**
     * @param $attribute
     * @param $params
     * @return void
     * @throws Exception
     */
    public function validateQuantityMax($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $order = $this->_order;
            foreach ($order->products as $product) {
                // Check kaspi
                if ($order->channel == OrderHelper::CHANNEL_KASPI_SHOP && $this->getQuantity($product->id) > $product->quantity) {
                    $this->addError($attribute, 'Для заказов Kaspi запрещено редактировать количество в большую сторону');
                    break;
                }

                // Check wolt
                if ($order->channel == OrderHelper::CHANNEL_WOLT && $this->getQuantity($product->id) > $product->quantity) {
                    $this->addError($attribute, 'Для заказов Wolt запрещено редактировать количество в большую сторону');
                    break;
                }
            }
        }
    }

    /**
     * @param $id
     * @return float
     * @throws Exception
     */
    protected function getQuantity($id): float
    {
        return (float)ArrayHelper::getValue($this->products, $id, 0);
    }
}