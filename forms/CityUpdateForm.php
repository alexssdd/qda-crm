<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\City;

/**
 * Class CityUpdateForm
 * @package app\forms
 */
class CityUpdateForm extends Model
{
    public $country_id;
    public $name;
    public $name_kk;
    public $config;
    public $status;

    /**
     * @param City $city
     * @param array $config
     */
    public function __construct(City $city, array $config = [])
    {
        $this->country_id = $city->country_id;
        $this->name = $city->name;
        $this->name_kk = $city->name_kk;
        $this->config = $city->config;
        $this->status = $city->status;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'country_id', 'status'], 'required'],
            [['country_id', 'status'], 'integer'],
            [['name', 'name_kk'], 'string'],
            [['config'], 'safe'],
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
