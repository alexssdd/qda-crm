<?php

namespace app\modules\order\helpers;

use Yii;
use app\modules\order\models\Order;
use app\modules\order\enums\OrderStatus;

/**
 * Order history helper
 */
class OrderHistoryHelper
{
    /**
     * @param Order $order
     * @return array[]
     */
    public static function getSteps(Order $order): array
    {
        $result = [
            'accepted' => [],
            'handled' => [],
            'finished' => []
        ];

        $timeCreated = $order->created_at;

        if ($order->source_at) {
            $timeCreated = $order->source_at;
        }

        $timeAccepted = null;
        $timeHandled = null;
        $timeFinished = null;

        $ruleAccepted = [30, 60]; // 30 minutes good, 1 hours normal
        $ruleHandled = [30, 60]; // 30 minutes good, 1 hours normal
        $ruleFinished = [600, 1440]; // 10 hours good, 24 hours normal

        foreach ($order->histories as $history) {
            if (!$timeAccepted && $history->status_after == OrderStatus::NEW->value){
                $timeAccepted = $history->created_at;
            }
            if ($history->status_after == OrderStatus::PROGRESS->value){
                $timeHandled = $history->created_at;
            }
            if (in_array($history->status_after, [OrderStatus::COMPLETED->value, OrderStatus::CANCELLED->value])){
                $timeFinished = $history->created_at;
            }
        }

        if ($timeAccepted){
            $diff = $timeAccepted - $timeCreated;
            $result['accepted'] = [
                'diff' => static::getShortDurationMinutes($diff),
                'status' => self::getRuleStatus($ruleAccepted, $diff)
            ];
        }
        if ($timeHandled){
            $diff = $timeHandled - max($timeCreated, $timeAccepted);
            $result['handled'] = [
                'diff' => static::getShortDurationMinutes($diff),
                'status' => self::getRuleStatus($ruleHandled, $diff)
            ];
        }

        if ($timeFinished){
            $diff = $timeFinished - max($timeCreated, $timeAccepted, $timeHandled);
            $result['finished'] = [
                'diff' => static::getShortDurationMinutes($diff),
                'status' => self::getRuleStatus($ruleFinished, $diff)
            ];
        }

        return $result;
    }

    /**
     * @param $ruleArray
     * @param $diff
     * @return string
     */
    protected static function getRuleStatus($ruleArray, $diff): string
    {
        if (!$ruleArray){
            return 'good';
        }

        $ruleCount = count(array_filter($ruleArray, function ($rule) use ($diff){
            return ($rule * 60) >= $diff;
        }));

        if ($ruleCount === 0){
            return 'bad';
        }
        if ($ruleCount === 1){
            return 'normal';
        }

        return 'good';
    }

    public static function getShortDurationMinutes($value): string
    {
        $duration = $value;

        if ($value >= 60){
            $duration = round($value / 60) * 60;
        }

        return Yii::$app->formatter->asDuration($duration);
    }
}