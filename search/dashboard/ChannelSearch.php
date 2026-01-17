<?php

namespace app\search\dashboard;

use Yii;
use Exception;
use yii\db\Query;
use yii\base\Model;
use app\entities\Order;
use app\entities\Store;
use app\entities\Stock;
use yii\helpers\ArrayHelper;
use app\entities\OrderProduct;
use app\core\helpers\CityHelper;
use app\core\helpers\StoreHelper;
use app\core\helpers\OrderHelper;
use app\core\behaviors\DateRangeBehavior;

/**
 * Class ProductSearch
 * @package app\search\dashboard
 */
class ChannelSearch extends Model
{
    /** Attributes */
    public $date_range;
    public $date_from;
    public $date_to;
    public $channel;

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'dateStartAttribute' => 'date_from',
                'dateEndAttribute' => 'date_to',
            ]
        ];
    }

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['channel'], 'integer'],
            [['date_from', 'date_to'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/']
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function export(): array
    {
        // Variables
        $stores = [];
        $channels = [];

        // Prepare cities
        $cities = [];
        foreach ($stores as $store){
            if (!array_key_exists($store->city_id, $cities)){
                $cities[$store->city_id] = [
                    'name' => $store->city->name,
                    'total_stock' => rand(50000, 10000),
                    'total_channels' => rand(5, count($channels)),
                    'kaspi_export' => rand(50000, 10000),
                    'kaspi_stock' => rand(50000, 10000),
                    'wb_export' => rand(50000, 10000),
                    'wb_stock' => rand(50000, 10000),
                    'ozon_export' => rand(50000, 10000),
                    'ozon_stock' => rand(50000, 10000),
                    'wolt_export' => rand(50000, 10000),
                    'wolt_stock' => rand(50000, 10000),
                    'all' => array_map(function ($channelName){
                        return [
                            'name' => $channelName,
                            'export' => rand(50000, 10000),
                            'export_label' => Yii::$app->formatter->asDecimal(rand(50000, 10000)),
                            'stock' => rand(50000, 10000),
                            'stock_label' => Yii::$app->formatter->asDecimal(rand(50000, 10000)),
                        ];
                    }, $channels),
                    'stores' => []
                ];
            }

            $cities[$store->city_id]['stores'][$store->id] = [
                'name' => $store->name,
                'total_stock' => rand(50000, 10000),
                'total_channels' => rand(5, count($channels)),
                'kaspi_export' => rand(50000, 10000),
                'kaspi_stock' => rand(50000, 10000),
                'wb_export' => rand(50000, 10000),
                'wb_stock' => rand(50000, 10000),
                'ozon_export' => rand(50000, 10000),
                'ozon_stock' => rand(50000, 10000),
                'wolt_export' => rand(50000, 10000),
                'wolt_stock' => rand(50000, 10000),
                'all' => array_map(function ($channelName){
                    return [
                        'name' => $channelName,
                        'export' => rand(50000, 10000),
                        'export_label' => Yii::$app->formatter->asDecimal(rand(50000, 10000)),
                        'stock' => rand(50000, 10000),
                        'stock_label' => Yii::$app->formatter->asDecimal(rand(50000, 10000)),
                    ];
                }, $channels)
            ];
        }

        // Prepare result
        $result = [
            CityHelper::ID_ALMATY => [],
            CityHelper::ID_ASTANA => [],
            CityHelper::ID_SHYMKENT => [],
            'others' => [
                'name' => 'Другие',
                'stores' => []
            ]
        ];
        foreach ($cities as $id => $city){
            if (array_key_exists($id, $result)){
                $result[$id] = $city;
            } else {
                $result['others']['stores'] = array_merge($result['others']['stores'], $city['stores']);
            }
        }

        return $result;
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function product($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        // Check channel
        if (!$this->channel){
            return [];
        }

        // Check date
        if (($this->date_to - $this->date_from) > 90 * 86400){
            $this->addError('date_range', 'Формирование отчета на период больше 90 дней не возможен');
            return [];
        }

        // Sales
        $data = (new Query())
            ->select([
                'orderProduct.sku AS sku',
                'orderProduct.name AS name',
                'SUM(orderProduct.quantity) AS quantity',
                'SUM(orderProduct.quantity * orderProduct.price) AS sum',
            ])
            ->from(['orders' => Order::tableName()])
            ->leftJoin(['orderProduct' => OrderProduct::tableName()], implode(' AND ', [
                'orders.id = orderProduct.order_id',
                'orderProduct.quantity > 0'
            ]))
            ->andWhere(['orders.channel' => $this->channel])
            ->andWhere(['orders.status' => [OrderHelper::STATUS_DELIVERED, OrderHelper::STATUS_ISSUED]])
            ->andWhere(['between', 'orders.created_at', $this->date_from, $this->date_to])
            ->groupBy([
                'orderProduct.sku',
                'orderProduct.name',
            ])
            ->orderBy([
                'sum' => SORT_DESC
            ])
            ->all();

        if (!$data){
            return [];
        }

        $cities = [
            CityHelper::ID_ALMATY => 0,
            CityHelper::ID_ASTANA => 0,
            CityHelper::ID_SHYMKENT => 0,
            'others' => 0
        ];
        $total = array_sum(ArrayHelper::getColumn($data, 'sum'));
        if ($total <= 0){
            return [];
        }
        foreach ($data as &$itemChanged){
            $itemChanged['percent'] = round(($itemChanged['sum'] / $total) * 100, 2);
        }

        // Sort
        ArrayHelper::multisort($data, 'percent', SORT_DESC);

        // Slice
        $data = array_slice($data, 0, 100);

        // Stocks
        $stocks = (new Query())
            ->select([
                'sku',
                'city_id',
                'quantity',
            ])
            ->from(Stock::tableName())
            ->andWhere(['sku' => ArrayHelper::getColumn($data, 'sku')])
            ->andWhere(['>', 'quantity', 0])
            ->all();
        $productStocks = [];
        foreach ($stocks as $stock) {
            if (!array_key_exists($stock['sku'], $productStocks)){
                $productStocks[$stock['sku']] = [];
            }

            $productStocks[$stock['sku']][$stock['city_id']] = (float)$stock['quantity'];
        }

        $result = [];
        foreach ($data as $item){
            $productStock = ArrayHelper::getValue($productStocks, $item['sku'], []);
            $cityStocks = $cities;

            foreach ($productStock as $cityId => $cityStock){
                if (array_key_exists($cityId, $cityStocks)){
                    $cityStocks[$cityId] += $cityStock;
                } else {
                    $cityStocks['others'] += $cityStock;
                }
            }

            // Add result
            $result[] = [
                'sku' => $item['sku'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'sum' => $item['sum'],
                'percent' => $item['percent'],
                'cityStocks' => $cityStocks
            ];
        }

        return $result;
    }

    /**
     * @param $params
     * @return array
     */
    public function sale($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        return [];
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

    /**
     * @return string[]
     */
    protected function getDemoProducts(): array
    {
        return [
            1 => 'LEGO Marvel Super Heroes PS4',
            2 => 'LEGO Batman 3 Beyond Gotham PS4',
            3 => 'LEGO: Набор для творчества большого размера Classic 10698',
            4 => 'LEGO: Набор для творчества среднего размера Classic 10696',
            5 => 'LEGO Мир Юрского Периода PS4',
            6 => 'LEGO Marvel Мстители PS4',
            7 => 'LEGO Harry Potter Collection PS4',
            8 => 'LEGO: Грозный динозавр CREATOR 31058',
            9 => 'LEGO Batman Movie. Я - Бэтмен! Дневник Тёмного рыцаря',
            10 => 'LEGO Worlds PS4'
        ];
    }

    /**
     * @param bool $onlyModels
     * @return array|Store[]
     * @throws Exception
     */
    protected function getDemoStores(bool $onlyModels = false): array
    {
        $stores = Store::find()
            ->with(['city'])
            ->andWhere(['status' => StoreHelper::STATUS_ACTIVE])
            ->all();

        if ($onlyModels){
            return $stores;
        }

        $result = [];

        foreach ($stores as $store){
            if (!array_key_exists($store->city_id, $result)){
                $result[$store->city_id] = [
                    'name' => $store->city->name,
                    'stores' => []
                ];
            }
            $result[$store->city_id]['stores'][$store->id] = StoreHelper::getNameShort($store);
        }

        return $result;
    }
}