<?php

namespace app\core\helpers;

use app\entities\Order;

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
        $isPickup = $order->delivery_method == DeliveryHelper::DELIVERY_PICKUP;
        $result = [
            'accepted' => [],
            'handled' => [],
            'courier' => [],
            'finished' => []
        ];
        if ($isPickup){
            $result = [
                'accepted' => [],
                'handled' => [],
                'finished' => []
            ];
        }

        $timeCreated = $order->created_at;
        $timeAccepted = null;
        $timeHandled = null;
        $timeCourier = null;
        $timeFinished = null;

        $ruleAccepted = [30, 60]; // 30 minutes good, 1 hours normal
        $ruleHandled = [30, 60]; // 30 minutes good, 1 hours normal
        $ruleCourier = [30, 60]; // 30 minutes good, 1 hours normal
        $ruleFinished = [600, 1440]; // 10 hours good, 24 hours normal

        if ($isPickup){
            $ruleFinished = null; // for pickup always good
        }

        foreach ($order->histories as $history) {
            if (!$timeAccepted && $history->status_after == OrderHelper::STATUS_ACCEPTED){
                $timeAccepted = $history->created_at;
            }
            if (in_array($history->status_after, [OrderHelper::STATUS_SHIPPED, OrderHelper::STATUS_PICKUP])){
                $timeHandled = $history->created_at;
            }
            if ($history->status_after == OrderHelper::STATUS_COURIER){
                $timeCourier = $history->created_at;
            }
            if (in_array($history->status_after, [OrderHelper::STATUS_DELIVERED, OrderHelper::STATUS_ISSUED, OrderHelper::STATUS_CANCELLED])){
                $timeFinished = $history->created_at;
            }
        }

        if ($timeAccepted){
            $diff = $timeAccepted - $timeCreated;
            $result['accepted'] = [
                'diff' => DateHelper::getShortDurationMinutes($diff),
                'status' => self::getRuleStatus($ruleAccepted, $diff)
            ];
        }
        if ($timeHandled){
            $diff = $timeHandled - max($timeCreated, $timeAccepted);
            $result['handled'] = [
                'diff' => DateHelper::getShortDurationMinutes($diff),
                'status' => self::getRuleStatus($ruleHandled, $diff)
            ];
        }
        if ($timeCourier){
            $diff = $timeCourier - max($timeCreated, $timeAccepted, $timeHandled);
            $result['courier'] = [
                'diff' => DateHelper::getShortDurationMinutes($diff),
                'status' => self::getRuleStatus($ruleCourier, $diff)
            ];
        }
        if ($timeFinished){
            $diff = $timeFinished - max($timeCreated, $timeAccepted, $timeHandled, $timeCourier);
            $result['finished'] = [
                'diff' => DateHelper::getShortDurationMinutes($diff),
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
}