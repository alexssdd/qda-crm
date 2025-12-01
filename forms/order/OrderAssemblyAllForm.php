<?php

namespace app\forms\order;

use app\core\forms\Form;

/**
 * Order assembly all form
 */
class OrderAssemblyAllForm extends Form
{
    public $store_id;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['store_id'], 'integer']
        ];
    }
}