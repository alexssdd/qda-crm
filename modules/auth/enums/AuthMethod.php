<?php
namespace app\modules\auth\enums;

/**
 * Способы аутентификации.
 */
enum AuthMethod: int
{
    case OTP = 10;
    case PASSWORD = 11;
    case FACEBOOK = 12;
    case GOOGLE = 13;

    public function label(): string
    {
        return match($this) {
            self::PASSWORD => 'password',
            self::OTP => 'otp',
            self::FACEBOOK => 'facebook',
            self::GOOGLE   => 'google',
        };
    }

    /** @return array<int,string> mapping для форм и документации */
    public static function labels(): array
    {
        return array_map(
            fn(AuthMethod $m) => $m->label(),
            self::cases()
        );
    }
}