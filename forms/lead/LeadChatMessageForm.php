<?php

namespace app\forms\lead;

use app\core\forms\Form;

/**
 * Lead chat message form
 */
class LeadChatMessageForm extends Form
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