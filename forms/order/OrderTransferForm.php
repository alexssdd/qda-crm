<?php

namespace app\forms\order;

use Yii;
use app\entities\User;
use app\core\forms\Form;

/**
 * Order transfer form
 */
class OrderTransferForm extends Form
{
    public $executor_id;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['executor_id'], 'required'],
            [['executor_id'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'executor_id' => Yii::t('app', 'User ID')
        ];
    }

    /**
     * @return User|null
     */
    public function getExecutor(): ?User
    {
        return User::findOne($this->executor_id);
    }
}