<?php

namespace app\forms;

use Yii;
use Exception;
use yii\base\Model;
use app\entities\Store;
use app\core\helpers\StoreHelper;

/**
 * Class StoreUpdateForm
 * @package app\forms
 */
class StoreUpdateForm extends Model
{
    public $merchant_id;
    public $city_id;
    public $type;
    public $name;
    public $name_short;
    public $number;
    public $address;
    public $lat;
    public $lng;
    public $phone;
    public $working_time;
    public $delivery_number;
    public $status;
    public $two_gis_id;
    public $yandex_company_id;
    public $google_id;

    // Export
    public $kaspi_export;
    public $kaspi_id;
    public $ozon_export;
    public $ozon_id;
    public $wb_export;
    public $wb_id;
    public $wolt_export;
    public $wolt_id;
    public $glovo_export;
    public $glovo_id;
    public $ye_export;
    public $ye_id;
    public $halyk_export;
    public $halyk_id;
    public $jusan_export;
    public $jusan_id;
    public $forte_export;
    public $forte_id;
	public $app_id;
	public $ozon_id_mhv;
	public $account_id;
	public $wb_id_express;
	public $wb_id_pickup;

    /**
     * @param Store $store
     * @param array $config
     * @throws Exception
     */
    public function __construct(Store $store, array $config = [])
    {
        $this->merchant_id = $store->merchant_id;
        $this->city_id = $store->city_id;
        $this->type = StoreHelper::getTypeName($store->type);
        $this->name = $store->name;
        $this->name_short = StoreHelper::getNameShort($store);
        $this->number = $store->number;
        $this->address = $store->address;
        $this->lat = $store->lat;
        $this->lng = $store->lng;
        $this->status = $store->status;
        $this->phone = StoreHelper::getPhone($store);
        $this->working_time = StoreHelper::getWorkingTime($store);
        $this->delivery_number = StoreHelper::getDeliveryNumber($store);
        $this->two_gis_id = StoreHelper::getTwoGisId($store);
        $this->yandex_company_id = StoreHelper::getYandexCompanyId($store);
        $this->google_id = StoreHelper::getGoogleId($store);

        // Export
        $this->kaspi_export = StoreHelper::getKaspiExport($store);
        $this->kaspi_id = StoreHelper::getKaspiId($store);
        $this->ozon_export = StoreHelper::getOzonExport($store);
        $this->ozon_id = StoreHelper::getOzonId($store);
        $this->wb_export = StoreHelper::getWbExport($store);
        $this->wb_id = StoreHelper::getWbId($store);
        $this->wolt_export = StoreHelper::getWoltExport($store);
        $this->wolt_id = StoreHelper::getWoltId($store);
        $this->glovo_export = StoreHelper::getGlovoExport($store);
        $this->glovo_id = StoreHelper::getGlovoId($store);
        $this->ye_export = StoreHelper::getYeExport($store);
        $this->ye_id = StoreHelper::getYeId($store);
        $this->halyk_export = StoreHelper::getHalykExport($store);
        $this->halyk_id = StoreHelper::getHalykId($store);
        $this->jusan_export = StoreHelper::getJusanExport($store);
        $this->jusan_id = StoreHelper::getJusanId($store);
        $this->forte_export = StoreHelper::getForteExport($store);
        $this->forte_id = StoreHelper::getForteId($store);
        $this->app_id = StoreHelper::getAppId($store);
        $this->ozon_id_mhv = StoreHelper::getOzonIdMhv($store);
        $this->wb_id_express = StoreHelper::getWbIdExpress($store);
        $this->wb_id_pickup = StoreHelper::getWbIdPickup($store);

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status'], 'integer'],
            [['name', 'number', 'address', 'lat', 'lng', 'status'], 'required'],
            [['name', 'name_short', 'number', 'address', 'lat', 'lng', 'phone', 'working_time', 'delivery_number'], 'string'],
            [['two_gis_id', 'yandex_company_id', 'google_id'], 'safe'],

            // Export
            [[
                'kaspi_export', 'kaspi_id', 'ozon_export', 'ozon_id', 'wb_export', 'wb_id',
                'wolt_export', 'wolt_id', 'glovo_export', 'glovo_id', 'ye_export', 'ye_id',
                'halyk_export', 'halyk_id', 'jusan_export', 'jusan_id', 'forte_export', 'forte_id',
				'app_id', 'ozon_id_mhv', 'wb_id_express', 'wb_id_pickup',
            ], 'safe']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'merchant_id' => Yii::t('app', 'Merchant ID'),
            'city_id' => Yii::t('app', 'City ID'),
            'type' => Yii::t('app', 'Type'),
            'name' => Yii::t('app', 'Name'),
            'name_short' => Yii::t('app', 'Name Short'),
            'number' => Yii::t('app', 'Number'),
            'address' => Yii::t('app', 'Address'),
            'lat' => Yii::t('app', 'Lat'),
            'lng' => Yii::t('app', 'Lng'),
            'phone' => Yii::t('app', 'Phone'),
            'working_time' => Yii::t('app', 'Working Time'),
            'delivery_number' => Yii::t('app', 'Logistic Number'),
            'status' => Yii::t('app', 'Status'),
            'two_gis_id' => Yii::t('app', 'Two Gis ID'),
            'yandex_company_id' => Yii::t('app', 'Yandex Company ID'),
            'google_id' => Yii::t('app', 'Google ID'),

            // Export
            'kaspi_export' => Yii::t('app', 'Kaspi Export'),
            'kaspi_id' => Yii::t('app', 'Kaspi ID'),
            'ozon_export' => Yii::t('app', 'Ozon Export'),
            'ozon_id' => Yii::t('app', 'Ozon ID'),
            'wb_export' => Yii::t('app', 'Wb Export'),
            'wb_id' => Yii::t('app', 'Wb ID'),
            'wolt_export' => Yii::t('app', 'Wolt Export'),
            'wolt_id' => Yii::t('app', 'Wolt ID'),
            'glovo_export' => Yii::t('app', 'Glovo Export'),
            'glovo_id' => Yii::t('app', 'Glovo ID'),
            'ye_export' => Yii::t('app', 'Ye Export'),
            'ye_id' => Yii::t('app', 'Ye ID'),
            'halyk_export' => Yii::t('app', 'Halyk Export'),
            'halyk_id' => Yii::t('app', 'Halyk ID'),
            'jusan_export' => Yii::t('app', 'Jusan Export'),
            'jusan_id' => Yii::t('app', 'Jusan ID'),
            'forte_export' => Yii::t('app', 'Forte Export'),
            'forte_id' => Yii::t('app', 'Forte ID'),
            'app_id' => Yii::t('app', 'App ID'),
            'ozon_id_mhv' => Yii::t('app', 'Ozon MHV ID'),
            'wb_id_express' => Yii::t('app', 'Wb Express ID'),
            'wb_id_pickup' => Yii::t('app', 'Wb Pickup ID'),
        ];
    }
}
