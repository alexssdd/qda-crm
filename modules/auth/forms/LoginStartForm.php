<?php
namespace app\modules\auth\forms;

use yii\base\Model;
use app\modules\auth\models\User;
use app\core\helpers\PhoneHelper;
use app\core\validators\PhoneValidator;

class LoginStartForm extends Model
{
    public $phone;
    private $_user;

    public function rules(): array
    {
        return [
            ['phone', 'required'],
            ['phone', PhoneValidator::class],
            ['phone', 'validateUserExists'],
        ];
    }

    public function validateUserExists($attribute): void
    {
        if (!$this->getUser()) {
            $this->addError($attribute, 'Пользователь не найден.');
        }
    }

    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $phone = PhoneHelper::getCleanNumber($this->phone);
            $this->_user = User::findByPhone($phone);
        }
        return $this->_user;
    }
}