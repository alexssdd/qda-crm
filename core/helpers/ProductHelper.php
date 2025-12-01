<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\Html;
use app\entities\Product;
use yii\helpers\ArrayHelper;
use app\entities\ProductExport;

/**
 * Product helper
 */
class ProductHelper
{
    /** Statuses */
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 11;

    /** Channels */
    const CHANNEL_KASPI = 10;
    const CHANNEL_OZON = 11;
    const CHANNEL_WB = 12;
    const CHANNEL_WOLT = 13;
    const CHANNEL_GLOVO = 14;
    const CHANNEL_YE = 15;
    const CHANNEL_HALYK = 16;
    const CHANNEL_JUSAN = 17;
    const CHANNEL_FORTE = 18;

    const PACKAGE_SKU = 1498600;

    /**
     * @return array status labels indexed by status values
     */
    public static function getStatusArray(): array
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'STATUS_ACTIVE'),
            self::STATUS_INACTIVE => Yii::t('app', 'STATUS_INACTIVE')
        ];
    }

    /**
     * @param $status
     * @return string
     * @throws Exception
     */
    public static function getStatusLabel($status): string
    {
        switch ($status) {
            case self::STATUS_ACTIVE:
                $class = 'label label-success';
                break;
            case self::STATUS_INACTIVE:
                $class = 'label label-danger';
                break;
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(self::getStatusArray(), $status), [
            'class' => $class,
        ]);
    }

    /**
     * @param Product $product
     * @return mixed
     * @throws Exception
     */
    public static function getImageLink(Product $product)
    {
        return ArrayHelper::getValue($product->config, 'images.0');
    }

    /**
     * @param Product $product
     * @return mixed
     * @throws Exception
     */
    public static function getType(Product $product)
    {
        return ArrayHelper::getValue($product->config, 'type', 'ITEM');
    }

    /**
     * @param Product $product
     * @return mixed
     * @throws Exception
     */
    public static function getM3(Product $product)
    {
        return ArrayHelper::getValue($product->config, 'm3');
    }

    /**
     * @param Product $product
     * @return mixed
     * @throws Exception
     */
    public static function getSizes(Product $product)
    {
        return ArrayHelper::getValue($product->config, 'sizes');
    }

    /**
     * @param Product $product
     * @return mixed
     * @throws Exception
     */
    public static function getWeight(Product $product)
    {
        return ArrayHelper::getValue($product->config, 'weight');
    }

    /**
     * @param Product $product
     * @return mixed
     * @throws Exception
     */
    public static function getBlock(Product $product)
    {
        return ArrayHelper::getValue($product->config, 'block', 0);
    }

    /**
     * @param $sku
     * @return string
     */
    public static function getCode($sku): string
    {
        return $sku;
        return ltrim($sku, '0');
    }

    /**
     * @param Product|null $product
     * @return string|null
     */
    public static function getBrand(Product $product = null): ?string
    {
        if (!$product) {
            return null;
        }
        return $product->brand ? $product->brand->name : null;
    }

    /**
     * @param $price
     * @return float
     */
    public static function getPrice($price = null): float
    {
        return (float)$price;
    }

    /**
     * @param Product $product
     * @return ProductExport[]
     */
    public static function getExports(Product $product): array
    {
        $exports = $product->exports;

        return ArrayHelper::index($exports, 'channel');
    }
}