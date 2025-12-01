<?php

namespace app\search\chart;

use Exception;
use yii\base\Model;
use app\entities\Order;
use app\entities\Product;
use yii\helpers\ArrayHelper;
use app\entities\OrderProduct;
use app\entities\ProductContent;
use app\core\helpers\OrderHelper;
use app\core\helpers\CategoryHelper;

/**
 * Class ProductSearch
 * @package app\search\chart
 */
class ProductSearch extends Model
{
    /** Attributes */
    public $date_from;
    public $date_to;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['date_from', 'date_to'], 'safe'],
            [['date_from', 'date_to'], 'validatePeriod']
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validatePeriod($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->date_from && !$this->date_to){
                $this->addError($attribute, 'Empty period');
            }
        }
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function category($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        $query = OrderProduct::find()
            ->alias('order_product')
            ->select([
                'content.category_id AS category_id',
                'SUM(order_product.quantity * order_product.price) AS sum',
            ])
            ->leftJoin(['order' => Order::tableName()], 'order_product.order_id = order.id')
            ->leftJoin(['product' => Product::tableName()], 'order_product.product_id = product.id')
            ->leftJoin(['content' => ProductContent::tableName()], 'product.id = content.product_id')
            ->andWhere([
                'order.status' => [OrderHelper::STATUS_DELIVERED, OrderHelper::STATUS_ISSUED],
            ])
            ->andWhere(['BETWEEN', 'order.created_at', $this->getDateFrom(), $this->getDateTo()])
            ->groupBy(['content.category_id'])
            ->asArray();

        $data = $query->all();
        $result = [];
        $superCategories = CategoryHelper::getSuperCategories();

        foreach ($data as $item) {
            $name = ArrayHelper::getValue($superCategories, $item['category_id'], 'Без категории');

            if (!array_key_exists($name, $result)){
                $result[$name] = [
                    'name' => $name,
                    'sum' => 0
                ];
            }
            $result[$name]['sum'] += (float)$item['sum'];
        }

        $result = array_values($result);

        // Sort
        ArrayHelper::multisort($result, 'sum', SORT_DESC);

        return $result;
    }

    /**
     * @return false|int
     */
    protected function getDateFrom()
    {
        return strtotime($this->date_from . ' 00:00:00');
    }

    /**
     * @return false|int
     */
    protected function getDateTo()
    {
        return strtotime($this->date_to . ' 23:59:59');
    }
}