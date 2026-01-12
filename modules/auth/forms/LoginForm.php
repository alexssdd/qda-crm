<?php
namespace app\modules\auth\forms;

use app\core\forms\Form;
use app\modules\auth\models\User;
use app\core\helpers\PhoneHelper;
use app\core\validators\PhoneValidator;

class LoginForm extends Form
{
    public $phone;
    private $_user;

    public function rules(): array
    {
        return [
            ['phone', 'required'],
            ['phone', PhoneValidator::class],
        ];
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