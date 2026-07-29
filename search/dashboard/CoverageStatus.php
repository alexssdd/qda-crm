<?php

namespace app\search\dashboard;

enum CoverageStatus: string
{
    case CRITICAL = 'critical';
    case LOW = 'low';
    case OK = 'ok';
    case READY = 'ready';
}
