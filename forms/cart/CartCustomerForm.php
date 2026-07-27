<?php

namespace app\forms\cart;

use Yii;
use app\entities\City;
use app\core\forms\Form;
use app\core\helpers\PhoneHelper;

/**
 * Cart customer form
 */
class CartCustomerForm extends Form
{
    public $city_id;
    public $phone;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['phone'], 'filter', 'filter' => [PhoneHelper::class, 'getCleanNumber']],
            [['phone'], 'required'],
            [['phone'], 'match',
                'pattern' => '/^7\d{10}$/',
                'message' => Yii::t('user', 'Phone must contain 11 digits and start with 7.'),
                'enableClientValidation' => false,
            ],
            [['city_id'], 'integer'],
            [['city_id'], 'exist', 'targetClass' => City::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }
}
