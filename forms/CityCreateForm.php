<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\Country;
use app\core\helpers\CityHelper;
use app\core\validators\CityConfigValidator;

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
            [['name', 'name_kk'], 'trim'],
            [['name', 'country_id', 'status'], 'required'],
            [['country_id', 'status'], 'integer'],
            [['country_id'], 'exist', 'targetClass' => Country::class, 'targetAttribute' => 'id'],
            [['status'], 'in', 'range' => array_keys(CityHelper::getStatusArray())],
            [['name', 'name_kk'], 'string', 'max' => 255],
            [['config'], CityConfigValidator::class],
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
