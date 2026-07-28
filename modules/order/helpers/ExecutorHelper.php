<?php

namespace app\modules\order\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\location\models\Country;
use app\modules\order\enums\ExecutorStatus;
use app\modules\order\helpers\OrderHelper;
use app\modules\order\models\Executor;

class ExecutorHelper
{
    public static function getStatuses(): array
    {
        return [
            ExecutorStatus::INACTIVE->value => Yii::t('app', 'EXECUTOR_STATUS_INACTIVE'),
            ExecutorStatus::ACTIVE->value => Yii::t('app', 'EXECUTOR_STATUS_ACTIVE'),
            ExecutorStatus::BLOCKED->value => Yii::t('app', 'EXECUTOR_STATUS_BLOCKED'),
        ];
    }

    public static function getStatusLabel(int $status): string
    {
        $class = match ($status) {
            ExecutorStatus::ACTIVE->value => 'label label-success',
            ExecutorStatus::INACTIVE->value => 'label label-default',
            ExecutorStatus::BLOCKED->value => 'label label-danger',
            default => 'label label-default',
        };

        return Html::tag('span', ArrayHelper::getValue(self::getStatuses(), $status, (string) $status), [
            'class' => $class,
        ]);
    }

    public static function getVerificationOptions(): array
    {
        return [
            0 => Yii::t('app', 'BOOL_NO'),
            1 => Yii::t('app', 'BOOL_YES'),
        ];
    }

    public static function getVerificationLabel(bool $isVerified): string
    {
        $class = $isVerified ? 'label label-success' : 'label label-default';
        $label = $isVerified ? Yii::t('app', 'BOOL_YES') : Yii::t('app', 'BOOL_NO');

        return Html::tag('span', $label, ['class' => $class]);
    }

    public static function getCountries(): array
    {
        return Country::find()
            ->select(['name', 'code'])
            ->orderBy(['sort' => SORT_ASC, 'name' => SORT_ASC])
            ->indexBy('code')
            ->column();
    }

    public static function getServiceTypes(): array
    {
        return OrderHelper::getTypes();
    }

    public static function getServiceNames(Executor $executor): string
    {
        $types = self::getServiceTypes();
        $result = [];

        foreach ($executor->services as $service) {
            $result[] = $types[(int) $service->type] ?? (string) $service->type;
        }

        return implode(', ', array_unique($result));
    }
}
