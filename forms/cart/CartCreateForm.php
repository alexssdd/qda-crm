<?php

namespace app\forms\cart;

use Yii;
use Exception;
use yii\helpers\Html;
use yii\helpers\Json;
use app\entities\Lead;
use app\entities\City;
use app\entities\Store;
use app\entities\Product;
use app\entities\Customer;
use app\entities\Merchant;
use app\core\forms\Form;
use app\core\helpers\UserHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\DeliveryHelper;
use app\core\helpers\PhoneHelper;
use app\core\helpers\AddressSelectHelper;

/**
 * Cart create form
 */
class CartCreateForm extends Form
{
    /** Fields */
    public $merchant_id;
    public $created_by;
    public $city_id;
    public $customer_id;
    public $phone;
    public $phone_ext;
    public $name;
    public $delivery_method;
    public $payment_method;
    public $store_id;
    public $address;
    public $lat;
    public $lng;
    public $house;
    public $apartment;
    public $intercom;
    public $entrance;
    public $floor;
    public $address_type;
    public $address_title;
    public $comment;
    public $delivery_cost;
    public $delivery_quote;
    public $products;
    public $lead_id;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['phone', 'phone_ext'], 'filter', 'filter' => [PhoneHelper::class, 'getCleanNumber']],
            [['name', 'address', 'address_title', 'house', 'apartment', 'intercom', 'entrance', 'floor', 'comment'], 'trim'],
            [['merchant_id', 'city_id', 'phone', 'name', 'delivery_method', 'payment_method'], 'required'],
            [['merchant_id', 'city_id', 'customer_id', 'delivery_method', 'payment_method', 'store_id', 'lead_id'], 'integer'],
            [['merchant_id'], 'exist', 'targetClass' => Merchant::class, 'targetAttribute' => 'id'],
            [['city_id'], 'exist', 'targetClass' => City::class, 'targetAttribute' => 'id'],
            [['customer_id'], 'exist', 'targetClass' => Customer::class, 'targetAttribute' => 'id'],
            [['lead_id'], 'exist', 'targetClass' => Lead::class, 'targetAttribute' => 'id'],
            [['delivery_method'], 'in', 'range' => array_keys(DeliveryHelper::getMethods())],
            [['phone', 'phone_ext'], 'string', 'max' => 21],
            [['phone'], 'match',
                'pattern' => '/^7\d{10}$/',
                'message' => Yii::t('user', 'Phone must contain 11 digits and start with 7.'),
                'enableClientValidation' => false,
            ],
            [['phone_ext'], 'match',
                'pattern' => '/^7\d{10}$/',
                'message' => Yii::t('user', 'Phone must contain 11 digits and start with 7.'),
                'enableClientValidation' => false,
            ],
            [['name', 'address', 'address_title'], 'string', 'max' => 255],
            [['house', 'apartment', 'intercom', 'entrance', 'floor'], 'string', 'max' => 50],
            [['comment'], 'string', 'max' => 1000],
            [['delivery_quote'], 'string', 'max' => 16384],
            [['lat'], 'number', 'min' => -90, 'max' => 90],
            [['lng'], 'number', 'min' => -180, 'max' => 180],
            [['delivery_cost'], 'number', 'min' => 0],
            [['address_type'], 'in', 'range' => [
                AddressSelectHelper::TYPE_MAP,
                AddressSelectHelper::TYPE_INPUT,
                AddressSelectHelper::TYPE_LIST,
            ]],
            [['address', 'lat', 'lng'], 'required', 'when' => function() {
                return in_array($this->delivery_method, DeliveryHelper::getMethodsForAddress());
            }, 'whenClient' => $this->whenClientAddress()],
            [['delivery_quote'], 'required', 'when' => function() {
                return in_array($this->delivery_method, DeliveryHelper::getMethodsForAddress());
            }, 'whenClient' => $this->whenClientAddress()],
            [['store_id'], 'required', 'when' => function() {
                return in_array($this->delivery_method, DeliveryHelper::getMethodsForStore());
            }, 'whenClient' => $this->whenClientStore()],
            [['store_id'], 'validateStore'],
            [['customer_id'], 'validateCustomer'],
            [['products'], 'validateProducts'],
        ];
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return 'Cart';
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'merchant_id' => Yii::t('cart', 'Merchant ID'),
            'city_id' => Yii::t('cart', 'City ID'),
            'customer_id' => Yii::t('cart', 'Customer ID'),
            'phone' => Yii::t('cart', 'Phone'),
            'name' => Yii::t('cart', 'Name'),
            'store_id' => Yii::t('cart', 'Store ID'),
            'delivery_method' => Yii::t('cart', 'Delivery Method'),
            'payment_method' => Yii::t('cart', 'Payment Method'),
            'address' => Yii::t('cart', 'Address'),
            'comment' => Yii::t('cart', 'Comment'),
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateProducts($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        if (!is_array($this->products) || !$this->products || count($this->products) > 100) {
            $this->addError($attribute, Yii::t('cart', 'Empty products'));
            return;
        }

        $productIds = [];
        foreach ($this->products as $id => $item) {
            if (
                filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false
                || !is_array($item)
                || !isset($item['quantity'])
                || !is_numeric($item['quantity'])
                || (float) $item['quantity'] <= 0
                || (float) $item['quantity'] > 100000
            ) {
                $this->addError($attribute, 'Некорректные данные товара');
                return;
            }

            $productIds[] = (int) $id;
        }

        $productCount = Product::find()
            ->andWhere([
                'id' => $productIds,
                'merchant_id' => $this->merchant_id,
            ])
            ->count();

        if ((int) $productCount !== count($productIds)) {
            $this->addError($attribute, 'Товар не соответствует выбранному магазину');
        }
    }

    public function validateStore($attribute): void
    {
        if ($this->hasErrors() || !$this->store_id) {
            return;
        }

        $store = Store::findOne([
            'id' => $this->store_id,
            'merchant_id' => $this->merchant_id,
            'city_id' => $this->city_id,
        ]);

        if (!$store) {
            $this->addError($attribute, 'Некорректная точка продажи');
        }
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
     * @param $id
     * @return void
     * @throws Exception
     */
    public function loadLead($id)
    {
        if (!$id){
            return;
        }

        if (!$lead = Lead::findOne($id)){
            return;
        }

        $this->merchant_id = $lead->brand ? $lead->brand->getMerchantId() : null;
        $this->created_by = UserHelper::getIdentity()->id;
        $this->city_id = $lead->city_id;
        $this->customer_id = $lead->customer_id;
        $this->phone = $lead->phone;
        $this->name = $lead->name;
        $this->lead_id = $id;
    }

    /**
     * @return string
     */
    protected function whenClientAddress(): string
    {
        $id = Html::getInputId($this, 'delivery_method');
        $methods = Json::encode(DeliveryHelper::getMethodsForAddress());

        return "function (attribute, value) {
            return $methods.includes(parseInt($('#$id').val()));
        }";
    }

    /**
     * @return string
     */
    protected function whenClientStore(): string
    {
        $id = Html::getInputId($this, 'delivery_method');
        $methods = Json::encode(DeliveryHelper::getMethodsForStore());

        return "function (attribute, value) {
            return $methods.includes(parseInt($('#$id').val()));
        }";
    }
}
