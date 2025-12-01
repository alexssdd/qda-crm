<?php

namespace app\forms\order;

use Yii;
use app\core\forms\Form;
use app\entities\OrderStore;
use app\entities\OrderProduct;

/**
 * Order assembly form
 */
class OrderAssemblyForm extends Form
{
    public $quantities;

    private OrderProduct $_orderProduct;
    private $_data;

    /**
     * @param OrderProduct $orderProduct
     * @param array $config
     */
    public function __construct(OrderProduct $orderProduct, array $config = [])
    {
        $this->_orderProduct = $orderProduct;
        parent::__construct($config);
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['quantities'], 'validateQuantity']
        ];
    }

    /**
     * @param $attribute
     * @return void
     */
    public function validateQuantity($attribute)
    {
        if (!$this->hasErrors()) {
            $data = $this->getData();
            $count = array_sum($data);

            if ($count > $this->_orderProduct->quantity || $count < $this->_orderProduct->quantity) {
                $this->addError($attribute, Yii::t(
                    'order',
                    'Incorrect quantity {product}, required: {required}, current: {current}',
                    [
                        'product' => $this->_orderProduct->name,
                        'required' => $this->_orderProduct->quantity,
                        'current' => $count
                    ]
                ));
            }
        }
    }

    /**
     * Only quantity > 0
     * @return array
     */
    public function getData(): array
    {
        if ($this->_data === null) {
            // Need for not skip current store
            $currentStoreIds = OrderStore::find()
                ->select(['store_id'])
                ->andWhere(['order_id' => $this->_orderProduct->order_id])
                ->column();

            $result = [];
            foreach ($this->quantities as $storeId => $quantity) {
                if ($quantity <= 0 && !in_array($storeId, $currentStoreIds)) {
                    continue;
                }
                $result[$storeId] = $quantity;
            }
            $this->_data = $result;
        }
        return $this->_data;
    }
}