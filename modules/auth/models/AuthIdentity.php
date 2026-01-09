<?php
namespace app\modules\auth\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * @property int $id Первичный ключ
 * @property int $user_id ID пользователя (FK на user.id)
 * @property string $type Тип метода ('password','otp','facebook' и т.п.)
 * @property string $identifier Идентификатор метода (email, телефон, соц. ID)
 * @property string|null $credential Учётные данные (хэш пароля, токен соцсети и т.д.)
 * @property bool $verified Флаг: метод подтверждён
 * @property int|null $verified_at UNIX‑timestamp времени верификации
 * @property int $created_at UNIX‑timestamp создания записи
 *
 * @property-read User   $user         Связанный пользователь
 */
class AuthIdentity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%auth_identity}}';
    }

    /**
     * Помечает запись как верифицированную и сохраняет время.
     */
    public function markVerified(): void
    {
        $this->verified = true;
        $this->verified_at = time();
        $this->save(false, ['verified', 'verified_at']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}