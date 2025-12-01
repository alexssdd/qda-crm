<?php

namespace app\forms;

use Yii;
use yii\base\Model;

/**
 * Class ContractCreateForm
 * @package app\forms
 */
class ContractCreateForm extends Model
{
    public $customer_id;
    public $customer_name;
    public $merchant_id;
    public $number;
    public $status;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['merchant_id', 'number', 'status'], 'required'],
            [['merchant_id', 'status'], 'integer'],
            [['number'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'customer_name' => Yii::t('app', 'Customer ID'),
            'merchant_id' => Yii::t('app', 'Merchant ID'),
            'number' => Yii::t('app', 'Number'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
