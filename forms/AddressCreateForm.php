<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\City;
use app\core\helpers\AddressHelper;

/**
 * Class AddressCreateForm
 * @package app\forms
 */
class AddressCreateForm extends Model
{
    public $customer_id;
    public $customer_name;
    public $city_id;
    public $address;
    public $lat;
    public $lng;
    public $status;

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
