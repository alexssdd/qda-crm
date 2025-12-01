<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\Contract;

/**
 * Class ContractUpdateForm
 * @package app\forms
 */
class ContractUpdateForm extends Model
{
    public $customer_name;
    public $merchant_name;
    public $number;
    public $status;

    /**
     * @param Contract $contract
     * @param array $config
     */
    public function __construct(Contract $contract, array $config = [])
    {
        $this->customer_name = $contract->customer ? $contract->customer->name : '';
        $this->merchant_name = $contract->merchant ? $contract->merchant->name : '';
        $this->number = $contract->number;
        $this->status = $contract->status;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status'], 'integer'],
            [['number'], 'string'],
            [['number', 'status'], 'required'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'customer_name' => Yii::t('app', 'Customer ID'),
            'merchant_name' => Yii::t('app', 'Merchant ID'),
            'number' => Yii::t('app', 'Number'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
