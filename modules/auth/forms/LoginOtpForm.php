<?php

namespace app\modules\auth\forms;

use Yii;
use app\core\forms\Form;
use app\modules\auth\models\User;
use app\core\helpers\PhoneHelper;
use app\modules\auth\models\AuthIdentity;
use app\modules\auth\models\AuthOtpCode;
use app\modules\auth\enums\AuthMethod;

class LoginOtpForm extends Form
{
    public string $phone = '';
    public string $code = '';
    public bool $rememberMe = true;

    private ?AuthIdentity $_identity = null;
    private ?AuthOtpCode $_otpCode = null;
    private ?User $_user = null;

    public function rules(): array
    {
        return [
            [['phone', 'code'], 'required', 'message' => 'Заполните поле'],

            // 1. Очистка телефона от маски +7(XXX)... -> 7XXX...
            ['phone', 'filter', 'filter' => function ($value) {
                return PhoneHelper::getCleanNumber($value);
            }],

            // 2. Проверка формата кода (6 цифр)
            ['code', 'match', 'pattern' => '/^\d{6}$/', 'message' => 'Код должен содержать 6 цифр'],

            // 3. Валидаторы логики
            ['phone', 'validateUserAndIdentity'], // Есть ли юзер и метод входа?
            ['code', 'validateOtpCode'],          // Верный ли код?
        ];
    }

    /**
     * Проверяем, есть ли юзер и Identity (OTP)
     */
    public function validateUserAndIdentity($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            // Сначала ищем Identity
            $identity = $this->getIdentity();

            if (!$identity) {
                $this->addError($attribute, 'Вход по коду недоступен для этого номера');
                return;
            }

            // Проверяем привязанного пользователя
            // (В твоем сервисе identity создается, но тут мы проверяем вход уже существующего)
            // Предполагаю связь hasOne 'user' в модели AuthIdentity или поиск через user_id
            $user = User::findOne($identity->user_id);
            if (!$user) {
                $this->addError($attribute, 'Пользователь не найден');
            } else {
                $this->_user = $user;
            }
        }
    }

    /**
     * Проверяем сам код (ХЕШ) и срок действия
     */
    public function validateOtpCode($attribute): void
    {
        if (!$this->hasErrors()) {
            $otpCode = $this->getOtpCode();

            if (!$otpCode) {
                $this->addError($attribute, 'Неверный код'); // Кода нет вообще
                return;
            }

            if ($otpCode->expires_at < time()) {
                $this->addError($attribute, 'Срок действия кода истек');
                return;
            }

            // === ВАЖНО: Проверка хеша как в сервисе ===
            if (!Yii::$app->security->validatePassword($this->code, $otpCode->code_hash)) {
                $this->addError($attribute, 'Неверный код');
            }
        }
    }

    /**
     * Вход в систему
     */
    public function login(): bool
    {
        if ($this->validate()) {
            // Удаляем код после успешного использования (как в сервисе $otpCode->delete())
            if ($this->_otpCode) {
                $this->_otpCode->delete();
            }

            // Логиним пользователя
            return Yii::$app->user->login($this->_user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    // --- Getters с кэшированием ---

    public function getIdentity(): ?AuthIdentity
    {
        if ($this->_identity === null) {
            $this->_identity = AuthIdentity::findOne([
                'type' => AuthMethod::OTP->value, // Используем значение из Enum
                'identifier' => $this->phone,
            ]);
        }
        return $this->_identity;
    }

    public function getOtpCode(): ?AuthOtpCode
    {
        // Ищем код только если нашли identity
        if ($this->_otpCode === null && $this->getIdentity()) {
            $this->_otpCode = AuthOtpCode::find()
                ->where(['identity_id' => $this->getIdentity()->id])
                // Можно добавить сортировку, чтобы брать последний, если вдруг их несколько
                // ->orderBy(['created_at' => SORT_DESC])
                ->one();
        }
        return $this->_otpCode;
    }
}