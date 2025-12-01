<?php

namespace app\core\helpers;

/**
 * Class JivositeHelper
 * @package app\core\helpers
 */
class JivositeHelper
{
    /** Events */
    const EVENT_CALL_EVENT = 'call_event';
    const EVENT_CHAT_ACCEPTED = 'chat_accepted';
    const EVENT_CHAT_ASSIGNED = 'chat_assigned';
    const EVENT_CHAT_FINISHED = 'chat_finished';
    const EVENT_CHAT_UPDATED = 'chat_updated';
    const EVENT_OFFLINE_MESSAGE = 'offline_message';
    const EVENT_CLIENT_UPDATED = 'client_updated';

    /** Statuses */
    const STATUS_ACTIVE = 10;
    const STATUS_FINISHED = 11;
}