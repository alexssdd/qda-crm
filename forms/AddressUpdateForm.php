<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\Address;

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
