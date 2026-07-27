<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\City;
use app\entities\Address;
use app\core\helpers\AddressHelper;

/**
 * Class AddressUpdateForm
 * @package app\forms
 */
class AddressUpdateForm extends Model
{
    public $customer_name;
    public $city_id;
    public $address;
    public $lat;
    public $lng;
    public $status;

    /**
     * @param Address $address
     * @param array $config
     */
    public function __construct(Address $address, array $config = [])
    {
        $this->customer_name = $address->customer ? $address->customer->name : '';
        $this->city_id = $address->city_id;
        $this->address = $address->address;
        $this->lat = $address->lat;
        $this->lng = $address->lng;
        $this->status = $address->status;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['address'], 'trim'],
            [['city_id', 'status'], 'integer'],
            [['city_id'], 'exist', 'targetClass' => City::class, 'targetAttribute' => 'id'],
            [['status'], 'in', 'range' => array_keys(AddressHelper::getStatusArray())],
            [['address'], 'string', 'max' => 255],
            [['lat'], 'number', 'min' => -90, 'max' => 90],
            [['lng'], 'number', 'min' => -180, 'max' => 180],
            [['city_id', 'address', 'status'], 'required'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'customer_name' => Yii::t('app', 'Customer ID'),
            'city_id' => Yii::t('app', 'City ID'),
            'address' => Yii::t('app', 'Address'),
            'lat' => Yii::t('app', 'Lat'),
            'lng' => Yii::t('app', 'Lng'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
