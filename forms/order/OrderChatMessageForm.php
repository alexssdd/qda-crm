<?php

namespace app\forms\order;

use app\core\forms\Form;

/**
 * Order chat message form
 */
class OrderChatMessageForm extends Form
{
    public $message;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['message'], 'trim'],
            [['message'], 'required'],
            [['message'], 'string', 'max' => 2000],
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
