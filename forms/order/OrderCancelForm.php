<?php

namespace app\forms\order;

use Exception;
use app\core\forms\Form;

/**
 * Order cancel form
 */
class OrderCancelForm extends Form
{
    public $reason;
    public $reason_additional;
    public $text;

    /**
     * @return string
     */
    public function formName(): string
    {
        return 'OrderCancel';
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function rules(): array
    {
        return [
            [['reason'], 'required'],
            [['reason_additional'], 'string'],
            [['text'], 'string']
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'reason' => 'Причина отмены',
            'reason_additional' => 'Дополнительная причина'
        ];
    }
}