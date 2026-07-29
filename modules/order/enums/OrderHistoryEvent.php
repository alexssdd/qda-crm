<?php

namespace app\modules\order\enums;

enum OrderHistoryEvent: string
{
    case BID_CREATE = 'bid.create';
    case EXECUTORS_NOTIFIED = 'executors.notified';
}
