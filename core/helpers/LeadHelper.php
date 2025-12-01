<?php

namespace app\core\helpers;

use Yii;
use Exception;
use app\entities\Lead;
use app\entities\Jivosite;
use yii\helpers\ArrayHelper;
use app\entities\JivositeEvent;

/**
 * Lead helper
 */
class LeadHelper
{
    /** Common channels (1 - 10) */
    const CHANNEL_CRM = 1;

    /** Jivosite channels (11 - 20) */
    const CHANNEL_JIVOSITE_SITE = 11;
    const CHANNEL_JIVOSITE_WHATSAPP = 12;
    const CHANNEL_JIVOSITE_INSTAGRAM = 13;

    /** Statuses */
    const STATUS_CREATED = 9;
    const STATUS_NEW = 10;
    const STATUS_PROCESS = 11;
    const STATUS_PENDING = 12;
    const STATUS_SUCCESS_B2B = 13;
    const STATUS_SUCCESS_B2C = 14;
    const STATUS_CLOSED = 15;
    const STATUS_CANCELLED = 16;

    /**
     * @return array
     */
    public static function getChannelArray(): array
    {
        return [
            self::CHANNEL_CRM => Yii::t('lead', 'CHANNEL_CRM'),
            self::CHANNEL_JIVOSITE_SITE => Yii::t('lead', 'CHANNEL_JIVOSITE_SITE'),
            self::CHANNEL_JIVOSITE_WHATSAPP => Yii::t('lead', 'CHANNEL_JIVOSITE_WHATSAPP'),
            self::CHANNEL_JIVOSITE_INSTAGRAM => Yii::t('lead', 'CHANNEL_JIVOSITE_INSTAGRAM')
        ];
    }

    /**
     * @param $channel
     * @return mixed
     * @throws Exception
     */
    public static function getChannelName($channel)
    {
        return ArrayHelper::getValue(self::getChannelArray(), $channel);
    }

    /**
     * @param $status
     * @return mixed
     * @throws Exception
     */
    public static function getChannelKey($status)
    {
        $keys = [
            self::CHANNEL_CRM => 'crm',
            self::CHANNEL_JIVOSITE_SITE => 'jivosite_site',
            self::CHANNEL_JIVOSITE_WHATSAPP => 'jivosite_whatsapp',
            self::CHANNEL_JIVOSITE_INSTAGRAM => 'jivosite_instagram'
        ];

        return ArrayHelper::getValue($keys, $status, 'undefined');
    }

    /**
     * @return int[]
     */
    public static function getJivositeChannels(): array
    {
        return [
            self::CHANNEL_JIVOSITE_SITE,
            self::CHANNEL_JIVOSITE_WHATSAPP,
            self::CHANNEL_JIVOSITE_INSTAGRAM
        ];
    }

    /**
     * @return array
     */
    public static function getStatusArray(): array
    {
        return [
            self::STATUS_CREATED => Yii::t('lead', 'STATUS_CREATED'),
            self::STATUS_NEW => Yii::t('lead', 'STATUS_NEW'),
            self::STATUS_PROCESS => Yii::t('lead', 'STATUS_PROCESS'),
            self::STATUS_PENDING => Yii::t('lead', 'STATUS_PENDING'),
            self::STATUS_SUCCESS_B2B => Yii::t('lead', 'STATUS_SUCCESS_B2B'),
            self::STATUS_SUCCESS_B2C => Yii::t('lead', 'STATUS_SUCCESS_B2C'),
            self::STATUS_CLOSED => Yii::t('lead', 'STATUS_CLOSED'),
            self::STATUS_CANCELLED => Yii::t('lead', 'STATUS_CANCELLED'),
        ];
    }

    /**
     * @return array
     */
    public static function getAvailableStatusArray(): array
    {
        return [
            self::STATUS_NEW => Yii::t('lead', 'STATUS_NEW'),
            self::STATUS_PROCESS => Yii::t('lead', 'STATUS_PROCESS'),
            self::STATUS_PENDING => Yii::t('lead', 'STATUS_PENDING'),
            self::STATUS_SUCCESS_B2B => Yii::t('lead', 'STATUS_SUCCESS_B2B'),
            self::STATUS_SUCCESS_B2C => Yii::t('lead', 'STATUS_SUCCESS_B2C'),
            self::STATUS_CLOSED => Yii::t('lead', 'STATUS_CLOSED'),
            self::STATUS_CANCELLED => Yii::t('lead', 'STATUS_CANCELLED'),
        ];
    }

    /**
     * @param $status
     * @return mixed
     * @throws Exception
     */
    public static function getStatusName($status)
    {
        return ArrayHelper::getValue(self::getStatusArray(), $status);
    }

    /**
     * @param $status
     * @return mixed
     * @throws Exception
     */
    public static function getStatusKey($status)
    {
        $keys = [
            self::STATUS_NEW => 'new',
            self::STATUS_PROCESS => 'process',
            self::STATUS_PENDING => 'pending',
            self::STATUS_SUCCESS_B2B => 'success_b2b',
            self::STATUS_SUCCESS_B2C => 'success_b2c',
            self::STATUS_CLOSED => 'closed',
            self::STATUS_CANCELLED => 'cancelled',
        ];

        return ArrayHelper::getValue($keys, $status, 'undefined');
    }

    /**
     * @return int[]
     */
    public static function getFinishedStatuses(): array
    {
        return [
            self::STATUS_SUCCESS_B2B,
            self::STATUS_SUCCESS_B2C,
            self::STATUS_CLOSED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * @param Lead $lead
     * @return string|null
     */
    public static function getHandlerName(Lead $lead): ?string
    {
        return $lead->handler ? $lead->handler->full_name : null;
    }

    /**
     * @param Lead $lead
     * @return string|null
     * @throws Exception
     */
    public static function getCreated(Lead $lead): ?string
    {
        return Yii::$app->formatter->asDatetime($lead->created_at);
    }

    /**
     * @param $status
     * @return bool
     */
    public static function isCompleted($status): bool
    {
        return in_array($status, self::getFinishedStatuses());
    }

    /**
     * @param Lead $lead
     * @return string|null
     */
    public static function getTitle(Lead $lead): ?string
    {
        if ($lead->title){
            return $lead->title;
        }
        if ($lead->name){
            return $lead->name;
        }
        if ($lead->customer){
            return $lead->customer->name;
        }

        return 'Лид №' . $lead->id;
    }

    /**
     * @param Lead $lead
     * @return array
     * @throws Exception
     */
    public static function getJivositeChat(Lead $lead): array
    {
        $chat = Jivosite::findOne(['chat_id' => $lead->vendor_id]);
        if (!$chat){
            return [];
        }

        /** @var JivositeEvent $finishedEvent */
        $finishedEvent = $chat->getJivositeEvents()
            ->andWhere('JSON_EXTRACT(data, "$.event_name") = :event', [
                ':event' => JivositeHelper::EVENT_CHAT_FINISHED
            ])->one();

        if (!$finishedEvent){
            return [];
        }

        $agents = ArrayHelper::getValue($finishedEvent->data, 'agents', []);
        $agents = ArrayHelper::index($agents, 'id');

        return [
            'agents' => $agents,
            'visitor' => ArrayHelper::getValue($finishedEvent->data, 'visitor.name'),
            'visitor_image' => ArrayHelper::getValue($finishedEvent->data, 'visitor.social.photos.0.url'),
            'messages' => ArrayHelper::getValue($finishedEvent->data, 'chat.messages', [])
        ];
    }
}