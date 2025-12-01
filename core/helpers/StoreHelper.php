<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\Html;
use app\entities\Store;
use yii\helpers\ArrayHelper;

/**
 * Store helper
 */
class StoreHelper
{
    /** Types */
    const TYPE_SHOP = 10;
    const TYPE_STORE = 11;

    /** Statuses */
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 11;

    /**
     * @return array
     */
    public static function getSelectArray(): array
    {
        $data = Store::find()->with(['city'])->all();

        return ArrayHelper::map($data, 'id', 'name', 'city.name');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getNameShort(Store $store): mixed
    {
        return ArrayHelper::getValue($store->config, 'name_short', $store->name);
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getPhone(Store $store): mixed
    {
        return ArrayHelper::getValue($store->config, 'phone');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getWorkingTime(Store $store): mixed
    {
        return ArrayHelper::getValue($store->config, 'working_time');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getDeliveryNumber(Store $store): mixed
    {
        return ArrayHelper::getValue($store->config, 'delivery_number');
    }

    /**
     * @return array
     */
    public static function getTypeArray(): array
    {
        return [
            self::TYPE_SHOP => Yii::t('app', 'TYPE_SHOP'),
            self::TYPE_STORE => Yii::t('app', 'TYPE_STORE')
        ];
    }

    /**
     * @param $type
     * @return string|null
     * @throws Exception
     */
    public static function getTypeName($type): ?string
    {
        return ArrayHelper::getValue(self::getTypeArray(), $type);
    }

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
        $class = match ($status) {
            self::STATUS_ACTIVE => 'label label-success',
            self::STATUS_INACTIVE => 'label label-danger',
            default => 'label label-default',
        };

        return Html::tag('span', ArrayHelper::getValue(self::getStatusArray(), $status), [
            'class' => $class,
        ]);
    }

    /**
     * @param $value
     * @return string|null
     */
    public static function getDurationLabel($value = null): ?string
    {
        if (!$value) {
            return null;
        }
        if ($value < 60){
            return $value . ' сек.';
        }

        $duration = round($value / 60) * 60;
        $durationParts = explode(' ', Yii::$app->formatter->asDuration($duration));

        if ($durationParts){
            return $durationParts[0] . ' мин.';
        }

        return $value;
    }

    /**
     * @param $value
     * @return string|null
     * @throws Exception
     */
    public static function getDistanceLabel($value = null): ?string
    {
        if (!$value) {
            return null;
        }
        return Yii::$app->formatter->asShortLength($value, 1);
    }

    /**
     * @param $duration
     * @param $distance
     * @return string|null
     * @throws Exception
     */
    public static function getDurationDistanceLabel($duration = null, $distance = null): ?string
    {
        $duration = static::getDurationLabel($duration);
        $distance = static::getDistanceLabel($distance);

        if ($duration && $distance) {
            return '(' . $duration . ', ' . $distance . ')';
        }
        return null;
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getTwoGisId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'two_gis_id');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getYandexCompanyId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'yandex_company_id');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getGoogleId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'google_id');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getKaspiExport(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'kaspi_export', DataHelper::BOOL_NO);
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getKaspiId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'kaspi_id');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getOzonExport(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'ozon_export', DataHelper::BOOL_NO);
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getOzonId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'ozon_id');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getWbExport(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'wb_export', DataHelper::BOOL_NO);
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getWbId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'wb_id');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getWoltExport(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'wolt_export', DataHelper::BOOL_NO);
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getWoltId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'wolt_id');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getGlovoExport(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'glovo_export', DataHelper::BOOL_NO);
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getGlovoId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'glovo_id');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getYeExport(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'ye_export', DataHelper::BOOL_NO);
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getYeId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'ye_id');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getHalykExport(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'halyk_export', DataHelper::BOOL_NO);
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getHalykId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'halyk_id');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getJusanExport(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'jusan_export', DataHelper::BOOL_NO);
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getJusanId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'jusan_id');
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getForteExport(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'forte_export', DataHelper::BOOL_NO);
    }

    /**
     * @param Store $store
     * @return mixed
     * @throws Exception
     */
    public static function getForteId(Store $store)
    {
        return ArrayHelper::getValue($store->config, 'forte_id');
    }

	/**
	 * @param Store $store
	 * @return mixed
	 * @throws Exception
	 */
	public static function getAppId(Store $store)
	{
		return ArrayHelper::getValue($store->config, 'app_id');
	}

	/**
	 * @param Store $store
	 * @return mixed
	 * @throws Exception
	 */
	public static function getOzonIdMhv(Store $store)
	{
		return ArrayHelper::getValue($store->config, 'ozon_id_mhv');
	}

	/**
	 * @param Store $store
	 * @return mixed
	 * @throws Exception
	 */
	public static function getWbIdExpress(Store $store)
	{
		return ArrayHelper::getValue($store->config, 'wb_id_express');
	}

	/**
	 * @param Store $store
	 * @return mixed
	 * @throws Exception
	 */
	public static function getWbIdPickup(Store $store)
	{
		return ArrayHelper::getValue($store->config, 'wb_id_pickup');
	}
}