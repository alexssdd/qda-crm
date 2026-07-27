<?php

namespace app\core\validators;

use DateTimeImmutable;
use yii\validators\Validator;

/**
 * Prevents invalid or excessively large reporting periods.
 */
class DatePeriodValidator extends Validator
{
    public string $startAttribute = 'date_from';
    public int $maxDays = 366;

    public function validateAttribute($model, $attribute): void
    {
        $from = DateTimeImmutable::createFromFormat('!Y-m-d', (string) $model->{$this->startAttribute});
        $to = DateTimeImmutable::createFromFormat('!Y-m-d', (string) $model->{$attribute});

        if (!$from || !$to) {
            return;
        }

        if ($from > $to) {
            $this->addError($model, $attribute, 'Дата окончания должна быть позже даты начала.');
            return;
        }

        if ($from->diff($to)->days > $this->maxDays) {
            $this->addError($model, $attribute, "Период не может превышать {$this->maxDays} дней.");
        }
    }
}
