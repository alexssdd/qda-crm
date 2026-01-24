<?php
namespace app\modules\order\enums;

use Yii;

enum OrderType: int
{
    case CARGO = 10;
    case TRUCK = 11;
    case EVACUATOR = 12;
    case MANIPULATOR = 13;
    case EQUIPMENT = 14;
    case TRAIN = 15;
}