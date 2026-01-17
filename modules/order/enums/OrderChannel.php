<?php

namespace app\modules\order\enums;

enum OrderChannel: int
{
    case CRM = 10;
    case BUSINESS = 11;
    case APP_IOS = 12;
    case APP_ANDROID = 13;
}