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
            [['text'], 'required']
        ];
    }

    protected function internalForms(): array
    {
        return ['chat'];
    }
}