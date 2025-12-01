<?php

namespace app\forms\appeal;

use Yii;
use app\core\forms\Form;

/**
 * Appeal create form
 */
class AppealCreateForm extends Form
{
    /** Fields */
    public $language;
    public $channel;
    public $created_by;
    public $city_id;
    public $customer_id;
    public $type;
    public $rating;
    public $order_number;
    public $phone;
    public $name;
    public $text;
    public $count_request;
    public $count_problem;
    public $delivery_late;
    public $complaint_object;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['city_id', 'type', 'channel', 'order_number', 'phone', 'name'], 'required'],
            [[
                'language', 'channel', 'city_id', 'customer_id', 'order_number', 'count_request', 'count_problem',
                'delivery_late', 'complaint_object', 'rating'
            ], 'integer'],
            [['phone', 'name'], 'string', 'max' => 255],
            [['text'], 'string']
        ];
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return 'Appeal';
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'language' => Yii::t('appeal', 'Language'),
            'city_id' => Yii::t('appeal', 'City ID'),
            'customer_id' => Yii::t('appeal', 'Customer ID'),
            'type' => Yii::t('appeal', 'Type'),
            'channel' => Yii::t('appeal', 'Channel'),
            'rating' => Yii::t('appeal', 'Rating'),
            'order_number' => Yii::t('appeal', 'Order Number'),
            'phone' => Yii::t('appeal', 'Phone'),
            'name' => Yii::t('appeal', 'Name'),
            'text' => Yii::t('appeal', 'Appeal Text'),
            'count_request' => Yii::t('appeal', 'Count Request'),
            'count_problem' => Yii::t('appeal', 'Count Problem'),
            'delivery_late' => Yii::t('appeal', 'Delivery Late'),
            'complaint_object' => Yii::t('appeal', 'Complaint Object'),
        ];
    }
}