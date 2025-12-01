<?php

namespace app\forms;

use Yii;
use Exception;
use yii\base\Model;
use app\entities\Advert;

/**
 * Class AdvertUpdateForm
 * @package app\forms
 */
class AdvertUpdateForm extends Model
{
    public $name;
    public $text;
    public $status;
    public $begin_at;
    public $end_at;

    /**
     * @param Advert $advert
     * @param array $config
     * @throws Exception
     */
    public function __construct(Advert $advert, array $config = [])
    {
        $this->name = $advert->name;
        $this->text = $advert->text;
        $this->status = $advert->status;
        $this->begin_at = Yii::$app->formatter->asDatetime($advert->begin_at, 'php:d.m.Y H:i');
        $this->end_at = Yii::$app->formatter->asDatetime($advert->end_at, 'php:d.m.Y H:i');

        parent::__construct($config);
    }

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
