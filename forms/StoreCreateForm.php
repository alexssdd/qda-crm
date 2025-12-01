<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\Store;
use app\entities\Merchant;
use app\core\helpers\DataHelper;
use app\core\helpers\StoreHelper;
use app\core\helpers\MerchantHelper;

/**
 * Store create form
 */
class StoreCreateForm extends Model
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

    /**
     * @param mixed $config
     */
    public function __construct($config = [])
    {
        $merchant = Merchant::findOne(['code' => MerchantHelper::CODE_MARWIN]);

        $this->merchant_id = $merchant?->id;
        $this->type = StoreHelper::TYPE_SHOP;
        $this->kaspi_export = DataHelper::BOOL_NO;
        $this->ozon_export = DataHelper::BOOL_NO;
        $this->wb_export = DataHelper::BOOL_NO;
        $this->wolt_export = DataHelper::BOOL_NO;
        $this->glovo_export = DataHelper::BOOL_NO;
        $this->ye_export = DataHelper::BOOL_NO;
        $this->halyk_export = DataHelper::BOOL_NO;
        $this->jusan_export = DataHelper::BOOL_NO;
        $this->forte_export = DataHelper::BOOL_NO;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'city_id', 'type', 'status'], 'integer'],
            [['name', 'name_short', 'number', 'address', 'lat', 'lng', 'phone', 'working_time', 'delivery_number'], 'string'],
            [['merchant_id', 'city_id', 'name', 'number', 'address', 'lat', 'lng', 'status'], 'required'],
            [['two_gis_id', 'yandex_company_id', 'google_id'], 'safe'],
            [['number'], 'unique', 'targetClass' => Store::class],

            // Export
            [[
                'kaspi_export', 'kaspi_id', 'ozon_export', 'ozon_id', 'wb_export', 'wb_id', 'wolt_export', 'wolt_id',
                'glovo_export', 'glovo_id', 'ye_export', 'ye_id', 'halyk_export', 'halyk_id', 'jusan_export', 'jusan_id',
                'forte_export', 'forte_id'
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
        ];
    }
}
