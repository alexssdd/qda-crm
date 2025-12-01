<?php

namespace app\forms\report;

use Yii;
use app\core\forms\Form;

/**
 * Class ReportCreateForm
 * @package app\forms\report
 */
class ReportCreateForm extends Form
{
    public $user_id;
    public $type;
    public $date_from;
    public $date_to;
    public $comment;
    public $params;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['user_id', 'type'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['comment'], 'string'],
            [['date_from', 'date_to', 'params'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'type' => Yii::t('app', 'Type'),
            'date_from' => Yii::t('app', 'Date From'),
            'date_to' => Yii::t('app', 'Date To'),
            'comment' => Yii::t('app', 'Comment'),
            'params' => Yii::t('app', 'Params'),
        ];
    }
}