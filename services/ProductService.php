<?php

namespace app\services;

use Yii;
use Exception;
use DomainException;
use app\entities\Price;
use app\entities\Product;
use app\entities\StoreStock;
use yii\helpers\ArrayHelper;
use app\entities\ProductExport;
use app\forms\ProductUpdateForm;
use app\core\helpers\ProductHelper;
use app\core\helpers\PriceTypeHelper;

/**
 * Product service
 */
class ProductService
{
    private $_model;

    /**
     * @param Product $model
     */
    public function __construct(Product $model)
    {
        $this->_model = $model;
    }

    /**
     * @param ProductUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(ProductUpdateForm $form): void
    {
        $model = $this->_model;

        // Transaction
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model->name = $form->name;
            $model->status = $form->status;
            if (!$model->save()) {
                throw new DomainException($model->getErrorSummary(true)[0]);
            }

            // Update exports
            $this->updateExports($form);

            // Transaction
            $transaction->commit();
        } catch (Exception $e){
            // Transaction
            $transaction->rollBack();

            throw $e;
        }
    }

    /**
     * @param $customerId
     * @return array|null
     */
    public function getPrice($customerId = null): ?array
    {
        // Variables
        $priceType = PriceTypeHelper::getPriceTypeCommon();

        /** @var Price $price */
        $price = Price::find()
            ->andWhere([
                'product_id' => $this->_model->id,
                'type_id' => $priceType->id
            ])
            ->one();

        if (!$price){
            return null;
        }

        return [
            'price_type_id' => $price->type_id,
            'price' => (float)$price->price
        ];
    }

    /**
     * @return array
     */
    public function getStocks(): array
    {
        $stocks = StoreStock::find()
            ->andWhere(['sku' => $this->_model->sku])
            ->with(['store.city'])
            ->all();

        $result = [];
        foreach ($stocks as $stock){
            $store = $stock->store;

            if (!array_key_exists($store->city_id, $result)){
                $result[$store->city_id] = [
                    'name' => $store->city->name,
                    'quantity' => 0,
                    'stores' => []
                ];
            }

            // Store
            $result[$store->city_id]['stores'][$store->id] = [
                'name' => $store->name,
                'quantity' => $stock->quantity
            ];

            // City
            $result[$store->city_id]['quantity'] += $stock->quantity;
        }

        return $result;
    }

    /**
     * @param ProductUpdateForm $form
     * @return void
     * @throws Exception
     */
    protected function updateExports(ProductUpdateForm $form): void
    {
        $model = $this->_model;

        $data = [
            ProductHelper::CHANNEL_KASPI => ['status' => $form->export_kaspi],
            ProductHelper::CHANNEL_OZON => ['status' => $form->export_ozon],
            ProductHelper::CHANNEL_WB => ['status' => $form->export_wb],
            ProductHelper::CHANNEL_WOLT => ['status' => $form->export_wolt],
            ProductHelper::CHANNEL_GLOVO => ['status' => $form->export_glovo],
            ProductHelper::CHANNEL_YE => ['status' => $form->export_ye],
            ProductHelper::CHANNEL_HALYK => ['status' => $form->export_halyk],
            ProductHelper::CHANNEL_JUSAN => ['status' => $form->export_jusan],
            ProductHelper::CHANNEL_FORTE => ['status' => $form->export_forte],
        ];
        $exports = ProductHelper::getExports($model);

        foreach ($data as $channel => $item){
            /** @var ProductExport $export */
            $export = ArrayHelper::getValue($exports, $channel);

            if (!$export){
                $export = new ProductExport();
                $export->product_id = $model->id;
                $export->sku = $model->sku;
                $export->channel = $channel;
            }

            $export->status = $item['status'];
            if (!$export->save()){
                throw new DomainException($export->getErrorSummary(true)[0]);
            }
        }
    }
}