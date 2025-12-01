<?php

namespace app\forms\cart;

use Yii;
use Exception;
use yii\helpers\Html;
use yii\helpers\Json;
use app\entities\Lead;
use app\core\forms\Form;
use app\core\helpers\UserHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\DeliveryHelper;

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
    public $products;
    public $lead_id;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'city_id', 'phone', 'name', 'delivery_method', 'payment_method'], 'required'],
            [['merchant_id', 'city_id', 'customer_id', 'delivery_method', 'payment_method', 'store_id'], 'integer'],
            [['phone', 'phone_ext', 'name'], 'string', 'max' => 255],
            [['comment'], 'string', 'max' => 1000],
            [['delivery_cost', 'house', 'apartment', 'intercom', 'entrance', 'floor', 'address_type', 'address_title', 'lead_id'], 'safe'],
            [['address', 'lat', 'lng'], 'required', 'when' => function() {
                return in_array($this->delivery_method, DeliveryHelper::getMethodsForAddress());
            }, 'whenClient' => $this->whenClientAddress()],
            [['store_id'], 'required', 'when' => function() {
                return in_array($this->delivery_method, DeliveryHelper::getMethodsForStore());
            }, 'whenClient' => $this->whenClientStore()],
            [['products'], 'safe'],
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
        if (!$this->hasErrors()) {
            if (empty($this->products)) {
                $this->addError($attribute, Yii::t('cart', 'Empty products'));
            }
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