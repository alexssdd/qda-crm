<?php

namespace app\modules\order\enums;

enum OrderHistoryEvent: string
{
    case BID_CREATE = 'bid.create';
    case MODERATION_COMPLETED = 'moderation.completed';
    case MODERATION_FAILED = 'moderation.failed';
    case EXECUTORS_NOTIFIED = 'executors.notified';
}
