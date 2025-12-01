<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\PriceType;

/**
 * Class PriceTypeUpdateForm
 * @package app\forms
 */
class PriceTypeUpdateForm extends Model
{
    public $code;
    public $name;
    public $status;

    /**
     * @param PriceType $priceType
     * @param array $config
     */
    public function __construct(PriceType $priceType, array $config = [])
    {
        $this->code = $priceType->code;
        $this->name = $priceType->name;
        $this->status = $priceType->status;

        parent::__construct($config);
    }

    /**
     * @return array
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
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
