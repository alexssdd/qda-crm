<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\core\helpers\CountryHelper;
use app\modules\location\models\Country;

/**
 * Country update form
 */
class CountryUpdateForm extends Model
{
    public $name;
    public $status;

    /**
     * @param Country $country
     * @param array $config
     */
    public function __construct(Country $country, array $config = [])
    {
        $this->name = $country->name;
        $this->status = $country->status;

        parent::__construct($config);
    }

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['name'], 'trim'],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => array_keys(CountryHelper::getStatusArray())],
            [['name'], 'string', 'max' => 255],
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
        ];
    }
}
