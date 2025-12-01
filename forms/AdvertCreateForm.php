<?php

namespace app\forms;

use Yii;
use yii\base\Model;

/**
 * Class AdvertCreateForm
 * @package app\forms
 */
class AdvertCreateForm extends Model
{
    public $name;
    public $text;
    public $status;
    public $begin_at;
    public $end_at;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status'], 'integer'],
            [['name', 'text'], 'string'],
            [['name', 'text', 'status'], 'required'],
            [['begin_at', 'end_at'], 'safe']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'Name'),
            'text' => Yii::t('app', 'Text'),
            'status' => Yii::t('app', 'Status'),
            'begin_at' => Yii::t('app', 'Begin At'),
            'end_at' => Yii::t('app', 'End At'),
        ];
    }
}
