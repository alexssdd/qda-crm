<?php

namespace app\modules\order\enums;

use Yii;

enum PriceType: string
{
    case FIXED_SEMI = 'fixed_semi';
    case FIXED = 'fixed';
    case REQUEST  = 'request';
    case CONTRACT = 'contract';

    public const FIXED_SEMI_ID = 9;
    public const FIXED_ID = 10;
    public const REQUEST_ID = 11;
    public const CONTRACT_ID = 12;

    public function getId(): int
    {
        return match ($this) {
            self::FIXED_SEMI    => self::FIXED_SEMI_ID,
            self::FIXED    => self::FIXED_ID,
            self::REQUEST  => self::REQUEST_ID,
            self::CONTRACT => self::CONTRACT_ID,
        };
    }
}