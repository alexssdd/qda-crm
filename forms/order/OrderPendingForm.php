<?php

namespace app\forms\order;

use Exception;
use app\core\forms\Form;

/**
 * Order pending form
 */
class OrderPendingForm extends Form
{
    public $reason;

    /**
     * @return array[]
     * @throws Exception
     */
    public function rules(): array
    {
        return [
            [['reason'], 'required']
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'reason' => 'Причина'
        ];
    }
}