<?php

namespace app\forms;

use Yii;
use yii\base\Model;

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
            [['city_id', 'status'], 'integer'],
            [['address', 'lat', 'lng'], 'string'],
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
