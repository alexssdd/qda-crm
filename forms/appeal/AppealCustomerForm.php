<?php

namespace app\forms\appeal;

use app\core\forms\Form;

/**
 * Appeal customer form
 */
class AppealCustomerForm extends Form
{
    public $phone;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['phone'], 'required']
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