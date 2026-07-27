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
