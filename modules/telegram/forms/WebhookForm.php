<?php

namespace app\modules\telegram\forms;

use app\core\forms\CompositeForm;

/**
 * @property ChatForm $chat
 */
class WebhookForm extends CompositeForm
{
    public $text;

    public function __construct(array $config = [])
    {
        $this->chat = new ChatForm();

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['text'], 'trim'],
            [['text'], 'required'],
            [['text'], 'string', 'max' => 4096],
        ];
    }

    protected function internalForms(): array
    {
        return ['chat'];
    }
}
