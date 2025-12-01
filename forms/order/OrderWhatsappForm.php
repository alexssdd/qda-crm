<?php

namespace app\forms\order;

use app\core\forms\Form;

/**
 * Order whatsapp
 */
class OrderWhatsappForm extends Form
{
    public $template;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['template'], 'required']
        ];
    }
}