<?php

namespace app\forms;

use Yii;
use yii\base\Model;

/**
 * Class BrandCreateForm
 * @package app\forms
 */
class BrandCreateForm extends Model
{
    public $name;
    public $status;
    public $merchant_id;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status', 'merchant_id'], 'integer'],
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
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'merchant_id' => Yii::t('app', 'Merchant ID'),
        ];
    }
}
