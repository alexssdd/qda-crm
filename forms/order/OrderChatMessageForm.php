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
            [['message'], 'required']
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