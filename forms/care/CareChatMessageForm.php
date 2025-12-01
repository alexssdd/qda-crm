<?php

namespace app\forms\care;

use app\core\forms\Form;

/**
 * Care chat message form
 */
class CareChatMessageForm extends Form
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