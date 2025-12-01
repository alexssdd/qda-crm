<?php

namespace app\forms\care;

use Yii;
use app\core\forms\Form;

/**
 * Care solution form
 */
class CareSolutionForm extends Form
{
    public $text;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['text'], 'required'],
            [['text'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'text' => Yii::t('care', 'Solution Text')
        ];
    }
}