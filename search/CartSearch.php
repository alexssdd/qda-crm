<?php

namespace app\search;

use Exception;
use yii\base\Model;
use app\entities\Price;
use app\entities\Stock;
use app\entities\Product;
use app\core\helpers\CityHelper;
use app\core\helpers\PriceTypeHelper;

/**
 * Cart search
 */
class CartSearch extends Model
{
    public $query;
    public $merchant_id;
    public $city_id;
    public $customer_id;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'city_id'], 'required'],
            [['merchant_id', 'city_id'], 'integer'],
            [['query'], 'string'],
            [['customer_id'], 'safe'],
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
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function search(array $params): array
    {
        $query = Product::find()
            ->alias('product')
            ->with(['brand'])
            ->limit(50);

        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        // Variables
        $priceType = PriceTypeHelper::getPriceTypeCommon();

        // Prepare price
        $query->select([
            'product.*',
            'IFNULL(IFNULL(price.price, priceAll.price), 0) AS price',
            'stock.quantity AS stock'
        ]);

        // Set price
        $query->leftJoin(['price' => Price::tableName()], implode(' AND ', [
            'product.id = price.product_id',
            'price.city_id = ' . $this->city_id,
            'price.type_id = ' . ($priceType ? $priceType->id : '0'),
        ]))->leftJoin(['priceAll' => Price::tableName()], implode(' AND ', [
            'product.id = priceAll.product_id',
            'priceAll.city_id = ' . CityHelper::ID_ALL,
            'priceAll.type_id = ' . ($priceType ? $priceType->id : '0')
        ]))->leftJoin(['stock' => Stock::tableName()], implode(' AND ', [
            'product.sku = stock.sku',
            'product.merchant_id = stock.merchant_id',
            'stock.city_id = ' . $this->city_id
        ]));

        // Filter by merchant
        $query->andWhere(['product.merchant_id' => $this->merchant_id]);

        // Filter by query
        if ($this->query){
            $query->andWhere([
                'or',
                ['like', 'product.name', $this->query],
                ['like', 'product.sku', $this->query],
            ]);
        }

        /** @var Product[] $data */
        $data = $query->all();
        $result = [];
        foreach ($data as $item) {
            if (!$price = (float)$item->price){
                continue;
            }

            $result[] = [
                'id' => $item->id,
                'sku' => $item->sku,
                'name' => $item->name,
                'brand' => $item->brand ? $item->brand->name : '',
                'barcode' => $item->barcode,
                'stock' => floor($item->stock ?: 0),
                'price' => $price,
            ];
        }

        return $result;
    }
}