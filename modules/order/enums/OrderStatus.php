<?php

namespace app\modules\order\enums;

enum OrderStatus: int
{
    case CREATED = 9;
    case NEW = 10;
    case PROGRESS = 11;
    case COMPLETED = 12;
    case CANCELLED = 13;
}
