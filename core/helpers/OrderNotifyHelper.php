<?php

namespace app\core\helpers;

/**
 * Order notify helper
 */
class OrderNotifyHelper
{
    /** Whatsapp templates */
    const WHATSAPP_PICKUP_READY = 'order_pickup_ready';
    const WHATSAPP_CANCELLED = 'order_cancelled';

    /**
     * @return string[]
     */
    public static function getWhatsappTemplates(): array
    {
        return [
            self::WHATSAPP_PICKUP_READY => 'Ваш заказ готов к выдаче',
            self::WHATSAPP_CANCELLED => 'Ваш заказ был отменен',
        ];
    }
}