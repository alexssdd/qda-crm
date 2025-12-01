<?php

namespace app\forms;

use Yii;
use yii\base\Model;

/**
 * Class CityCreateForm
 * @package app\forms
 */
class CityCreateForm extends Model
{
    public $country_id;
    public $name;
    public $name_kk;
    public $config;
    public $status;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'country_id', 'status'], 'required'],
            [['country_id', 'status'], 'integer'],
            [['name', 'name_kk'], 'string'],
            [['config'], 'safe']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'country_id' => Yii::t('app', 'Country ID'),
            'name' => Yii::t('app', 'Name'),
            'name_kk' => Yii::t('app', 'Name Kk'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
