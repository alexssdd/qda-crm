<?php

namespace app\core\helpers;

use Yii;
use DateTime;

/**
 * Date helper
 */
class DateHelper
{
    /**
     * @param $value
     * @return string
     */
    public static function getGmDate($value): string
    {
        $parts = [];

        // Days
        $days = floor($value / 86400);
        if ($days){
            $parts[] = Yii::t('app', '{delta, plural, =1{1 day} other{# days}}', ['delta' => $days]);
        }

        $parts[] = gmdate('H:i:s', $value);

        return implode(' ', $parts);
    }

    /**
     * @param $value
     * @return string
     */
    public static function getShortDurationMinutes($value): string
    {
        $duration = $value;

        if ($value >= 60){
            $duration = round($value / 60) * 60;
        }

        return Yii::$app->formatter->asDuration($duration);
    }

    /**
     * @param $week
     * @return string
     */
    public static function getWeekShortName($week): string
    {
        switch ($week){
            case '1': return 'Пн';
            case '2': return 'Вт';
            case '3': return 'Ср';
            case '4': return 'Чт';
            case '5': return 'Пт';
            case '6': return 'Сб';
            case '0': return 'Вс';
            default:
                return $week;
        }
    }

    /**
     * @param $month
     * @return string
     */
    public static function getMonthName($month): string
    {
        switch ($month){
            case '1': return 'Январь';
            case '2': return 'Февраль';
            case '3': return 'Март';
            case '4': return 'Апрель';
            case '5': return 'Май';
            case '6': return 'Июнь';
            case '7': return 'Июль';
            case '8': return 'Август';
            case '9': return 'Сентябрь';
            case '10': return 'Октябрь';
            case '11': return 'Ноябрь';
            case '12': return 'Декабрь';
            default:
                return $month;
        }
    }

    /**
     * @param $month
     * @return string
     */
    public static function getMonthShortName($month): string
    {
        switch ($month){
            case '1': return 'Янв';
            case '2': return 'Фев';
            case '3': return 'Мар';
            case '4': return 'Апр';
            case '5': return 'Май';
            case '6': return 'Июн';
            case '7': return 'Июл';
            case '8': return 'Авг';
            case '9': return 'Сен';
            case '10': return 'Окт';
            case '11': return 'Ноя';
            case '12': return 'Дек';
            default:
                return $month;
        }
    }

    /**
     * @param $dateTime
     * @param string $format
     * @param string $from
     * @return string
     */
    public static function format($dateTime, string $format = 'd.m.Y H:i:s', string $from = 'Y-m-d H:i:s'): string
    {
        return DateTime::createFromFormat($from, $dateTime)->format($format);
    }
}