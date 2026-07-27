<?php

namespace app\core\validators;

use yii\validators\Validator;

class CityConfigValidator extends Validator
{
    private const COORDINATES = [
        'lat' => [-90, 90],
        'lng' => [-180, 180],
    ];

    private const IDENTIFIERS = [
        'delivery_id',
        'kato',
        'forte_id',
    ];

    public function validateAttribute($model, $attribute): void
    {
        $value = $model->$attribute;

        if ($value === null || $value === '') {
            $model->$attribute = [];
            return;
        }

        if (!is_array($value)) {
            $this->addError($model, $attribute, 'Некорректные настройки города');
            return;
        }

        $result = [];

        foreach (self::COORDINATES as $key => [$min, $max]) {
            if (!array_key_exists($key, $value) || $value[$key] === '') {
                continue;
            }

            if (!is_numeric($value[$key]) || (float) $value[$key] < $min || (float) $value[$key] > $max) {
                $this->addError($model, $attribute, 'Некорректные координаты города');
                return;
            }

            $result[$key] = (float) $value[$key];
        }

        foreach (self::IDENTIFIERS as $key) {
            if (!array_key_exists($key, $value) || $value[$key] === '') {
                continue;
            }

            if (!is_scalar($value[$key])) {
                $this->addError($model, $attribute, 'Некорректный идентификатор города');
                return;
            }

            $identifier = trim((string) $value[$key]);
            if (mb_strlen($identifier) > 100) {
                $this->addError($model, $attribute, 'Идентификатор города слишком длинный');
                return;
            }

            $result[$key] = $identifier;
        }

        $model->$attribute = $result;
    }
}
