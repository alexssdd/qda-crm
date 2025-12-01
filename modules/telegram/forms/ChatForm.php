<?php

namespace app\modules\telegram\forms;

use app\core\forms\Form;

class ChatForm extends Form
{
    public $id;

    public function rules()
    {
        return [
            [['id'], 'required']
        ];
    }
}