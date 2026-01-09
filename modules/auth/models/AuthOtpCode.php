<?php
namespace app\modules\auth\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * @property int $identity_id PK, ссылка на запись в user_identity (type = otp)
 * @property string $code_hash Хэш одноразового кода
 * @property int $expires_at UNIX‑timestamp времени истечения кода
 * @property int $created_at UNIX‑timestamp времени создания кода
 *
 * @property-read AuthIdentity $identity Связанный метод аутентификации
 */
class AuthOtpCode extends ActiveRecord
{
    /** {@inheritdoc} */
    public static function tableName(): string
    {
        return '{{%auth_otp_code}}';
    }

    /**
     * Возвращает UserIdentity, к которому привязан этот OTP.
     *
     * @return ActiveQuery
     */
    public function getIdentity(): ActiveQuery
    {
        return $this->hasOne(AuthIdentity::class, ['id' => 'identity_id']);
    }
}