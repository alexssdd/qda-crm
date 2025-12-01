<?php

namespace app\forms;

use Yii;
use Exception;
use yii\base\Model;
use app\entities\Brand;

/**
 * Class BrandUpdateForm
 * @package app\forms
 */
class BrandUpdateForm extends Model
{
    public $name;
    public $status;
    public $merchant_id;

    /**
     * @param Brand $brand
     * @param array $config
     * @throws Exception
     */
    public function __construct(Brand $brand, array $config = [])
    {
        $this->name = $brand->name;
        $this->status = $brand->status;
        $this->merchant_id = $brand->getMerchantId();

        parent::__construct($config);
    }

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
