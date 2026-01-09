<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\entities\User;
use app\core\helpers\PhoneHelper;
use app\modules\auth\enums\AuthMethod;
use app\core\validators\PhoneValidator;
use app\modules\auth\models\AuthIdentity;
use app\modules\auth\models\AuthOtpCode;

class LoginForm extends Model
{
    public $phone;
    public $password;
    public $otp;
    public $rememberMe = true;

    // Скрытое поле, чтобы понимать, какой сценарий мы отрабатываем при финальном сабмите
    public $authType;

    private $_user;
    private $_identity;

    const SCENARIO_PASSWORD = 'password';
    const SCENARIO_OTP = 'otp';

    public function rules(): array
    {
        return [
            [['phone'], 'required'],
            ['phone', PhoneValidator::class],

            // Пароль обязателен только в сценарии пароля
            ['password', 'required', 'on' => self::SCENARIO_PASSWORD],
            ['password', 'validatePassword', 'on' => self::SCENARIO_PASSWORD],

            // OTP обязателен только в сценарии OTP
            ['otp', 'required', 'on' => self::SCENARIO_OTP],
            ['otp', 'validateOtp', 'on' => self::SCENARIO_OTP],

            ['rememberMe', 'boolean'],
            ['authType', 'string'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            $identity = $this->getIdentity(AuthMethod::PASSWORD->value);

            if (!$user || !$identity || !Yii::$app->security->validatePassword($this->password, $identity->credential)) {
                $this->addError($attribute, 'Неверный логин или пароль.');
            }
        }
    }

    public function validateOtp($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            $identity = $this->getIdentity(AuthMethod::OTP->value);

            if (!$user || !$identity) {
                $this->addError($attribute, 'Пользователь не найден.');
                return;
            }

            // Ищем актуальный код в таблице auth_otp_code
            // Предполагаем использование ActiveRecord
            $otpRecord = AuthOtpCode::find()
                ->where(['identity_id' => $identity->id])
                ->andWhere(['>', 'expires_at', time()])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            if (!$otpRecord || $otpRecord->code_hash !== $this->otp) {
                // Примечание: если в базе хэш, то нужно сверять через validateHash,
                // но для OTP часто хранят чистый код или простой хэш.
                // Если у тебя там хэш: !Yii::$app->security->validatePassword($this->otp, $otpRecord->code_hash)
                $this->addError($attribute, 'Неверный код или срок действия истек.');
            }
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    protected function getUser(): ?User
    {
        if ($this->_user === null) {
            $phone = PhoneHelper::getCleanNumber($this->phone);
            $this->_user = User::findByPhone($phone);
        }
        return $this->_user;
    }

    // Хелпер для получения нужной записи identity
    protected function getIdentity($type)
    {
        $user = $this->getUser();
        if ($user) {
            // Предполагаем связь hasMany в модели User
            // return $user->getIdentities()->where(['type' => $type])->one();
            // Либо прямой запрос, если связи нет:
            return AuthIdentity::find()->where(['user_id' => $user->id, 'type' => $type])->one();
        }
        return null;
    }
}