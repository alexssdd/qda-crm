<?php

namespace app\forms;

use Yii;
use Exception;
use app\entities\City;
use app\entities\Order;
use app\entities\Customer;
use app\core\forms\Form;
use app\entities\Address;
use app\core\helpers\PhoneHelper;

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
            [['phone'], 'filter', 'filter' => [PhoneHelper::class, 'getCleanNumber']],
            [['address', 'house', 'apartment', 'intercom', 'entrance', 'floor', 'title'], 'trim'],
            [['city_id', 'customer_id'], 'integer'],
            [['city_id'], 'exist', 'targetClass' => City::class, 'targetAttribute' => 'id'],
            [['phone'], 'string', 'max' => 21],
            [['phone'], 'match',
                'pattern' => '/^7\d{10}$/',
                'message' => Yii::t('user', 'Phone must contain 11 digits and start with 7.'),
                'enableClientValidation' => false,
            ],
            [['address', 'title'], 'string', 'max' => 255],
            [['house', 'apartment', 'intercom', 'entrance', 'floor'], 'string', 'max' => 50],
            [['lat'], 'number', 'min' => -90, 'max' => 90],
            [['lng'], 'number', 'min' => -180, 'max' => 180],
            [['type'], 'in', 'range' => [
                \app\core\helpers\AddressSelectHelper::TYPE_MAP,
                \app\core\helpers\AddressSelectHelper::TYPE_INPUT,
                \app\core\helpers\AddressSelectHelper::TYPE_LIST,
            ]],
            [['customer_id'], 'validateCustomer'],
        ];
    }

    public function validateCustomer($attribute): void
    {
        if ($this->hasErrors() || !$this->customer_id) {
            return;
        }

        if (!Customer::findOne(['id' => $this->customer_id, 'phone' => $this->phone])) {
            $this->addError($attribute, 'Клиент не соответствует номеру телефона');
        }
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
