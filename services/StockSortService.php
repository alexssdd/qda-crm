<?php

namespace app\services;

use Exception;
use yii\helpers\ArrayHelper;
use app\core\helpers\CityHelper;
use app\core\helpers\StockHelper;

/**
 * Stock sort service
 */
class StockSortService
{
    /** Sort */
    const SORT_CITY_SHOP = 1;
    const SORT_STORE_ALMATY = 2;
    const SORT_STORE_SHYMKENT = 3;
    const SORT_ALMATY_SHOP = 4;
    const SORT_ASTANA_SHOP = 5;
    const SORT_OTHER_SHOP = 6;

    private $_stores;
    private $_products;
    private $_sortType;
    private $_cityId;

    private $_sortedStores;

    /**
     * @param $stores
     * @param array $products
     * @param int $sortType
     * @param $cityId
     */
    public function __construct($stores, array $products = [], int $sortType = StockHelper::SORT_TYPE_DURATION, $cityId = null)
    {
        $this->_stores = $stores;
        $this->_products = $products;
        $this->_sortType = $sortType;
        $this->_cityId = $cityId;
    }

    /**
     * @return array|null
     */
    public function getStore(): ?array
    {
        $storeGroups = $this->sort();
        
        foreach ($storeGroups as $group => $stores){
            if ($group === 'pairs'){
                continue;
            }
            
            return array_shift($stores);
        }
        
        return null;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getStores(): array
    {
        $storeGroups = $this->sort();
        $allPairs = ArrayHelper::getValue($storeGroups, 'pairs', []);
        if (!$allPairs){
            return [];
        }

        // Fill percent for pairs
        $pairs = [];
        foreach ($allPairs as $pair) {
            $percent = 0;
            foreach ($this->_products as $product) {
                if ($product['quantity'] == 0){
                    continue;
                }
                if (!isset($pair['stocks'][$product['sku']])){
                    continue;
                }
                if ($pair['stocks'][$product['sku']] >= $product['quantity']){
                    $percent += 1;
                    continue;
                }
                $percent += $pair['stocks'][$product['sku']] / $product['quantity'];
            }
            $pair['percent'] = $percent;
            $pairs[] = $pair;
        }

        ArrayHelper::multisort($pairs, ['percent'], [SORT_DESC]);

        $result = [];
        $productStores = [];

        foreach ($this->_products as $product) {
            $quantityNeed = $product['quantity'];
            $quantityLeft = $product['quantity'];
            $sku = $product['sku'];

            foreach ($pairs as $store) {
                // Пропускаем если в точке нету данного товара
                if (empty($store['stocks'][$sku])){
                    continue;
                }

                // Если собрали нужное количество, переходим к следующему товару
                if ($quantityLeft == 0){
                    break;
                }

                $quantity = $store['stocks'][$sku];
                $quantitySet = $quantity;

                // Если в этой точке есть нужное количество, то сборку делаем только с этой точки
                if ($quantity >= $quantityNeed){
                    $quantityLeft = $quantityNeed;

                    // Также убираем другие точки, где была сборка
                    if (isset($productStores[$sku])){
                        foreach (ArrayHelper::getValue($productStores, $sku, []) as $storeId => $quantity) {
                            if (!isset($result[$storeId])){
                                continue;
                            }
                            unset($result[$storeId]['products'][$sku]);
                            if (!$result[$storeId]['products']){
                                unset($result[$storeId]);
                            }
                        }
                    }
                }

                // Заполнение первоначальных данных
                if (empty($result[$store['id']])){
                    $result[$store['id']] = $store;
                }
                if (empty($result[$store['id']]['products'][$sku])){
                    $result[$store['id']]['products'][$sku] = 0;
                }

                // Если в точке больше чем нужно
                if ($quantitySet > $quantityLeft){
                    $quantitySet = $quantityLeft;
                }

                // Сборка
                if ($quantityLeft > 0){
                    $result[$store['id']]['products'][$sku] += $quantitySet;
                    $quantityLeft -= $quantitySet;
                    $productStores[$sku][$store['id']] = $quantitySet;
                }
            }

            // Если товара не хватает во всех точках, возвращаем пустой результат
            if ($quantityLeft > 0){
                return [];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function sort(): array
    {
        if ($this->_sortedStores === null){
            $stores = $this->_stores;
            $this->_sortedStores = [
                'enough' => [],
                'pairs' => []
            ];
            
            // Group
            foreach ($stores['enough'] as &$store){
                $this->prepareForEmex($store);
                $store['group_index'] = 1;
                $this->_sortedStores['enough'][] = $store;
            }
            foreach ($stores['limited'] as &$store){
                $this->prepareForEmex($store);
                $store['group_index'] = 2;
                $this->_sortedStores['enough'][] = $store;
            }
            foreach ($stores['pairs'] as &$store){
                $this->prepareForEmex($store);
                $store['group_index'] = 3;
                $this->_sortedStores['pairs'][] = $store;
            }
            
            // Sort
            if ($this->_sortType == StockHelper::SORT_TYPE_STOCK){
                ArrayHelper::multisort($this->_sortedStores['enough'], ['stock', 'group_index'], [SORT_ASC, SORT_ASC]);
            } elseif ($this->_sortType == StockHelper::SORT_TYPE_EMEX){
                ArrayHelper::multisort($this->_sortedStores['enough'], ['sort_emex', 'group_index'], [SORT_ASC, SORT_ASC]);
            } else {
                ArrayHelper::multisort($this->_sortedStores['enough'], ['duration', 'group_index'], [SORT_ASC, SORT_ASC]);
            }
        }
        
        return $this->_sortedStores;
    }

    /**
     * @param $store
     * @return void
     */
    protected function prepareForEmex(&$store): void
    {
        if ($store['city_id'] == $this->_cityId){
            $store['sort_emex'] = self::SORT_CITY_SHOP;
        } else if ($store['number'] == 'ИМ_В_24'){
            $store['sort_emex'] = self::SORT_STORE_ALMATY;
        } elseif ($store['number'] == 'ШМББ_О_11'){
            $store['sort_emex'] = self::SORT_STORE_SHYMKENT;
        } elseif ($store['city_id'] == CityHelper::ID_ALMATY){
            $store['sort_emex'] = self::SORT_ALMATY_SHOP;
        } elseif ($store['city_id'] == CityHelper::ID_ASTANA){
            $store['sort_emex'] = self::SORT_ASTANA_SHOP;
        } else {
            $store['sort_emex'] = self::SORT_OTHER_SHOP;
        }
    }
}