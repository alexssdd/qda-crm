<?php

namespace app\modules\order\enums;

enum ExecutorStatus: int
{
    case INACTIVE = 9;
    case ACTIVE = 10;
    case BLOCKED = 11;
}