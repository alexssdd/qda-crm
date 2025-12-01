<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\PriceType;

/**
 * Class PriceTypeCreateForm
 * @package app\forms
 */
class PriceTypeCreateForm extends Model
{
    public $code;
    public $name;
    public $status;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status'], 'integer'],
            [['code', 'name'], 'string'],
            [['code', 'name', 'status'], 'required'],
            [['code'], 'unique', 'targetClass' => PriceType::class],
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
