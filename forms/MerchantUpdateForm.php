<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\Merchant;

/**
 * Class MerchantUpdateForm
 * @package app\forms
 */
class MerchantUpdateForm extends Model
{
    public $code;
    public $name;
    public $status;

    /**
     * @param Merchant $merchant
     * @param array $config
     */
    public function __construct(Merchant $merchant, array $config = [])
    {
        $this->code = $merchant->code;
        $this->name = $merchant->name;
        $this->status = $merchant->status;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['status'], 'integer'],
            [['name'], 'string'],
            [['name', 'status'], 'required'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
