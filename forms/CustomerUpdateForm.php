<?php

namespace app\forms;

use Yii;
use Exception;
use yii\base\Model;
use app\entities\Customer;
use app\core\helpers\PhoneHelper;
use app\core\helpers\CustomerHelper;

/**
 * Customer update form
 */
class CustomerUpdateForm extends Model
{
    public $parent_name;
    public $name;
    public $phone;
    public $email;
    public $ref;
    public $type_name;
    public $iin;
    public $status;

    /**
     * @param Customer $customer
     * @param array $config
     * @throws Exception
     */
    public function __construct(Customer $customer, array $config = [])
    {
        $this->parent_name = $customer->parent ? $customer->parent->name : '';
        $this->name = $customer->name;
        $this->phone = PhoneHelper::getMaskPhone($customer->phone);
        $this->email = $customer->email;
        $this->ref = $customer->ref;
        $this->type_name = CustomerHelper::getTypeName($customer->type);
        $this->iin = $customer->iin;
        $this->status = $customer->status;

        parent::__construct($config);
    }

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['status'], 'integer'],
            [['name'], 'string'],
            [['name', 'status'], 'required'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'parent_name' => Yii::t('app', 'Parent ID'),
            'name' => Yii::t('app', 'Name'),
            'phone' => Yii::t('app', 'Phone'),
            'email' => Yii::t('app', 'Email'),
            'ref' => Yii::t('app', 'Ref'),
            'type_name' => Yii::t('app', 'Type'),
            'iin' => Yii::t('app', 'Iin'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
