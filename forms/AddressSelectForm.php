<?php

namespace app\forms;

use Yii;
use Exception;
use app\entities\City;
use app\entities\Order;
use app\core\forms\Form;
use app\entities\Address;

/**
 * Address select form
 */
class AddressSelectForm extends Form
{
    public $city_id;
    public $customer_id;
    public $phone;
    public $address;
    public $lat;
    public $lng;
    public $house;
    public $apartment;
    public $intercom;
    public $entrance;
    public $floor;
    public $type;
    public $title;

    /**
     * @param Order|null $order
     * @param array $config
     * @throws Exception
     */
    public function __construct(Order $order = null, array $config = [])
    {
        if ($order){
            $this->city_id = $order->city_id;
            $this->customer_id = $order->customer_id;
            $this->phone = $order->phone;
            $this->address = $order->address;
            $this->lat = $order->lat;
            $this->lng = $order->lng;
            $this->house = $order->getHouse();
            $this->apartment = $order->getApartment();
            $this->intercom = $order->getIntercom();
            $this->entrance = $order->getEntrance();
            $this->floor = $order->getFloor();
            $this->type = $order->getAddressType();
            $this->title = $order->getAddressTitle();
        }

        parent::__construct($config);
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [[
                'city_id', 'customer_id', 'phone', 'address', 'lat', 'lng', 'house', 'apartment',
                'intercom', 'entrance', 'floor', 'type', 'address_title'
            ], 'safe']
        ];
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return 'Address';
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'address' => Yii::t('app', 'Address'),
            'house' => Yii::t('app', 'House'),
            'apartment' => Yii::t('app', 'Apartment'),
            'intercom' => Yii::t('app', 'Intercom'),
            'entrance' => Yii::t('app', 'Entrance'),
            'floor' => Yii::t('app', 'Floor'),
        ];
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return City::findOne($this->city_id);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getCustomerAddresses(): array
    {
        // Check customer ID
        if (!$this->customer_id){
            return [];
        }

        $addresses = Address::find()
            ->andWhere(['customer_id' => $this->customer_id])
            ->andWhere(['city_id' => $this->city_id])
            ->all();

        return array_map(function (Address $address){
            return [
                'address' => $address->address,
                'lat' => $address->lat,
                'lng' => $address->lng,
                'is_default' => $address->isDefault(),
                'title' => $address->getTitle(),
                'house' => $address->getHouse(),
                'apartment' => $address->getApartment(),
                'intercom' => $address->getIntercom(),
                'entrance' => $address->getEntrance(),
                'floor' => $address->getFloor(),
            ];
        }, $addresses);
    }
}