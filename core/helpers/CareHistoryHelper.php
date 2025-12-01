<?php

namespace app\core\helpers;

use app\entities\Care;

/**
 * Care history helper
 */
class CareHistoryHelper
{
    /**
     * @param Care $care
     * @return array[]
     */
    public static function getSteps(Care $care): array
    {
        $result = [
            'new' => [],
            'accepted' => [],
            'finished' => []
        ];

        $timeCreated = $care->created_at;
        $timeNew = null;
        $timeAccepted = null;
        $timeFinished = null;

        $ruleNew = [30, 60]; // 30 minutes good, 1 hours normal
        $ruleAccepted = [30, 60]; // 30 minutes good, 1 hours normal
        $ruleFinished = [600, 1440]; // 10 hours good, 24 hours normal

        foreach ($care->histories as $history) {
            if (!$timeNew && $history->status_after == CareHelper::STATUS_NEW){
                $timeNew = $history->created_at;
            }
            if ($history->status_after == CareHelper::STATUS_ACCEPTED){
                $timeAccepted = $history->created_at;
            }
            if (in_array($history->status_after, [CareHelper::STATUS_FINISHED_GOOD, CareHelper::STATUS_FINISHED_BAD, CareHelper::STATUS_COULD_NOT_CALL])){
                $timeFinished = $history->created_at;
            }
        }

        if ($timeNew){
            $diff = $timeNew - $timeCreated;
            $result['new'] = [
                'diff' => DateHelper::getShortDurationMinutes($diff),
                'status' => self::getRuleStatus($ruleNew, $diff)
            ];
        }
        if ($timeAccepted){
            $diff = $timeAccepted - max($timeCreated, $timeNew);
            $result['accepted'] = [
                'diff' => DateHelper::getShortDurationMinutes($diff),
                'status' => self::getRuleStatus($ruleAccepted, $diff)
            ];
        }
        if ($timeFinished){
            $diff = $timeFinished - max($timeCreated, $timeNew, $timeAccepted);
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