<?php
namespace app\modules\order\enums;

use Yii;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case KASPI_TRANSFER = 'kaspi_transfer';
    case VISA = 'visa';
    case BANK_TRANSFER = 'bank_transfer';

    public const CASH_ID = 10;
    public const KASPI_TRANSFER_ID = 11;
    public const VISA_ID = 12;
    public const BANK_TRANSFER_ID = 13;

    public static function getLabelById(int $id): string
    {
        return match ($id) {
            self::CASH_ID => Yii::t('app', 'order.payment_method.cash'),
            self::KASPI_TRANSFER_ID => Yii::t('app', 'order.payment_method.kaspi_transfer'),
            self::VISA_ID => Yii::t('app', 'order.payment_method.visa'),
            self::BANK_TRANSFER_ID => Yii::t('app', 'order.payment_method.bank_transfer'),
            default => Yii::t('app', 'order.payment_method.unknown'),
        };
    }
}
