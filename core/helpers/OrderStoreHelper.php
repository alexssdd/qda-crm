<?php

namespace app\core\helpers;

use Exception;
use app\entities\OrderStore;
use yii\helpers\ArrayHelper;
use app\entities\OrderStoreProduct;
use app\modules\wb\helpers\WbHelper;
use app\modules\ozon\helper\OzonHelper;
use app\modules\halyk\helpers\HalykMappingHelper;
use app\modules\kaspi\helpers\KaspiMappingHelper;

/**
 * Order store helper
 */
class OrderStoreHelper
{
    /** Types */
    const TYPE_SALE = 10;
    const TYPE_MOVE = 11;

    /** Statuses */
    const STATUS_NEW = 10;
    const STATUS_ASSEMBLED_PARTIAL = 11;
    const STATUS_ASSEMBLED = 12;
    const STATUS_COMPLETE = 13;
    const STATUS_CANCELED = 14;

    /**
     * @var array|string[]
     */
    private static array $mapping = [
        'АА_Р_01' => 'АА_Р_163',
        'АА_Р_119' => 'АА_Р_170',
        'АА_Р_15' => 'АА_Р_164',
        'АА_Р_175' => 'АА_Р_180',
        'АА_Р_176' => 'АА_Р_178',
        'АА_Р_183' => 'АА_Р_184',
        'АА_Р_188' => 'АА_Р_189',
        'АК_Р_01' => 'АК_Р_03',
        'АС_Р_02' => 'АС_Р_47',
        'АС_Р_153' => 'АС_Р_154',
        'АС_Р_155' => 'АС_Р_156',
        'АС_Р_157' => 'АС_Р_158',
        'АС_Р_19' => 'АС_Р_46',
        'АС_Р_28' => 'АС_Р_29',
        'АС_Р_32' => 'АС_Р_33',
        'АС_Р_71' => 'АС_Р_85',
        'АТ_Р_23' => 'АТ_Р_24',
        'АУ_Р_01' => 'АУ_Р_06',
        'ИМ_В_24' => 'ВБАА_Р_10',
        'КА_Р_04' => 'КА_Р_52',
        'КО_Р_01' => 'КО_Р_02',
        'ПА_Р_26' => 'ПА_Р_27',
        'ПТ_Р_01' => 'ПТ_Р_02',
        'СМ_Р_01' => 'СМ_Р_03',
        'ТА_Р_02' => 'ТА_Р_07',
        'УК_Р_03' => 'УК_Р_06',
        'УР_Р_03' => 'УР_Р_04',
        'УР_Р_05' => 'УР_Р_06',
        'ШМ_Р_03' => 'ШМ_Р_09',
        'ШМББ_О_11' => 'КСПИМ_Р_03', // todo
        'АТ_Р_01' => 'АТ_Р_04',
        'АУ_Р_08' => 'АУ_Р_11',
    ];

    /**
     * @var array|string[]
     */
    private static array $mappingKaspi = [
        'ШМББ_О_11' => 'КСПИМ_Р_03',
        'ИМ_В_24' => 'КСПИМ_Р_03',
        'ПА_Р_26' => 'ПА_Р_27',
        'АУ_Р_08' => 'КСПАУ_Р_04',
    ];

    /**
     * @var array|string[]
     */
    private static array $mappingOzon = [
        'ШМББ_О_11' => 'ИМ_Р_40',
        'ШМ_Р_03' => 'ИМ_Р_40',
        'ИМ_В_24' => 'ИМ_Р_40',
        'АА_Р_01' => 'ИМ_Р_40',
        'АА_Р_15' => 'ИМ_Р_40',
        'АС_Р_157' => 'ИМ_Р_40',
        'АС_Р_153' => 'ИМ_Р_40',
    ];

    /**
     * @var array|string[]
     */
    private static array $mappingWb = [
        'ШМББ_О_11' => 'ВБАА_Р_10',
        'ИМ_В_24' => 'ВБАА_Р_10',
		'АС_Р_157' => 'ВБАА_Р_10',
		'АА_Р_15' => 'ВБАА_Р_10'
    ];

	/**
	 * @var array|string[]
	 */
	private static array $mappingOzonMhv = [
		'ШМББ_О_11' => 'ИМ_Р_41',
		'ИМ_В_24' => 'ИМ_Р_41',
	];

	/**
	 * @var array|string[]
	 */
	private static array $mappingWbPTW = [
		'ШМББ_О_11' => 'ВБАА_Р_40',
		'ИМ_В_24' => 'ВБАА_Р_40',
	];

    /**
     * @param $status
     * @return string
     * @throws Exception
     */
    public static function getStatusName($status): string
    {
        $data = [
            self::STATUS_NEW => 'pending',
            self::STATUS_ASSEMBLED => 'assembled',
            self::STATUS_ASSEMBLED_PARTIAL => 'assembled_partial',
            self::STATUS_COMPLETE => 'complete',
            self::STATUS_CANCELED => 'canceled',
        ];

        return ArrayHelper::getValue($data, $status);
    }

    /**
     * @param OrderStore $orderStore
     * @return int
     */
    public static function getPriority(OrderStore $orderStore): int
    {
        $order = $orderStore->order;

        if ($order->channel == OrderHelper::CHANNEL_KASPI_SHOP && $order->delivery_method == DeliveryHelper::DELIVERY_PICKUP) {
            return 1900;
        }

        return match ($orderStore->order->delivery_method) {
            DeliveryHelper::DELIVERY_YANDEX_EDA => 5000,
            DeliveryHelper::DELIVERY_WOLT => 4600,
            DeliveryHelper::DELIVERY_GLOVO => 4500,
            DeliveryHelper::DELIVERY_KASPI_EXPRESS => 4000,
            DeliveryHelper::DELIVERY_HALYK => 3500,
            DeliveryHelper::DELIVERY_WB_EXPRESS => 3000,
            DeliveryHelper::DELIVERY_KASPI => 2000,
            DeliveryHelper::DELIVERY_JUSAN => 1770,
            DeliveryHelper::DELIVERY_WB => 1730,
            DeliveryHelper::DELIVERY_FORTE,
            DeliveryHelper::DELIVERY_FORTE_EXPRESS => 1700,
            DeliveryHelper::DELIVERY_OZON => 970,
            default => 1000,
        };
    }

    /**
     * @param OrderStoreProduct $orderStoreProduct
     * @return string|void
     */
    public static function getStatusCodeProduct(OrderStoreProduct $orderStoreProduct)
    {
        $orderStore = $orderStoreProduct->orderStore;

        if ($orderStore->status == self::STATUS_CANCELED) {
            return 'not_available';
        }

        if ($orderStore->status == self::STATUS_NEW) {
            return 'pending';
        }

        if ($orderStore->status == self::STATUS_ASSEMBLED_PARTIAL) {
            if ($orderStoreProduct->quantity == $orderStoreProduct->quantity_available) {
                return 'picked';
            }

            return 'not_available';
        }
    }

    /**
     * @param OrderStore $orderStore
     * @param $channel
     * @return string|null
     * @throws Exception
     */
    public static function getStoreVirtual(OrderStore $orderStore, $channel = null): ?string
    {
        $default = ArrayHelper::getValue(self::$mapping, $orderStore->store->number, $orderStore->store->number);
        $order = $orderStore->order;

        // Overwrite for kaspi
        if ($order->channel == OrderHelper::CHANNEL_KASPI_SHOP) {
            return KaspiMappingHelper::getStoreVirtual($orderStore);
        }

        // Overwrite for halyk
        if ($order->channel == OrderHelper::CHANNEL_HALYK_MARKET) {
            return HalykMappingHelper::getStoreVirtual($orderStore);
        }

        // Ozon
        if ($channel == OrderHelper::CHANNEL_OZON){
			if ($order->account_id == OzonHelper::ACCOUNT_MHV) {
				return 'ИМ_Р_41';
			}
			return 'ИМ_Р_40'; // always
        }

        // WB
        if ($channel == OrderHelper::CHANNEL_WB){
			if ($order->delivery_method == DeliveryHelper::DELIVERY_WB_EXPRESS) {
				return 'ВБАА_Р_15';
			}
			if ($order->account_id == WbHelper::ACCOUNT_PTW) {
				return ArrayHelper::getValue(self::$mappingWb, $orderStore->store->number, $default);
			}
            return ArrayHelper::getValue(self::$mappingWb, $orderStore->store->number, $default);
        }

        return $default;
    }
}