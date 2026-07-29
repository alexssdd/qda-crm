<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\modules\location\models\Country;
use app\modules\location\models\Location;
use app\modules\order\helpers\ExecutorHelper;
use app\modules\order\models\Executor;

class ExecutorUpdateForm extends Model
{
    public $location_id;
    public $service_types = [];

    private Executor $executor;

    public function __construct(Executor $executor, array $config = [])
    {
        $this->executor = $executor;
        $this->location_id = $executor->location_id;
        $this->service_types = array_map(
            static fn($service): int => (int) $service->type,
            $executor->services
        );

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [
                'location_id',
                'filter',
                'filter' => static fn($value): ?int => $value === '' || $value === null
                    ? null
                    : (int) $value,
            ],
            ['location_id', 'validateLocation'],
            [
                'service_types',
                'filter',
                'filter' => static function ($value): array {
                    if (!is_array($value)) {
                        return [];
                    }

                    $types = array_values(array_unique(array_map('intval', $value)));
                    sort($types);

                    return $types;
                },
            ],
            [
                'service_types',
                'each',
                'rule' => [
                    'in',
                    'range' => array_keys(ExecutorHelper::getServiceTypes()),
                    'strict' => true,
                ],
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'location_id' => Yii::t('app', 'Location'),
            'service_types' => Yii::t('app', 'Services'),
        ];
    }

    public function validateLocation(string $attribute): void
    {
        if ($this->{$attribute} === null) {
            return;
        }

        $exists = Location::find()
            ->alias('location')
            ->innerJoin(
                ['country' => Country::tableName()],
                '[[country.id]] = [[location.country_id]]'
            )
            ->where([
                'location.id' => $this->{$attribute},
                'country.code' => $this->executor->country_code,
            ])
            ->exists();

        if (!$exists) {
            $this->addError($attribute, Yii::t('app', 'Location is invalid.'));
        }
    }

    public function getExecutorName(): string
    {
        return (string) $this->executor->name;
    }

    public function getLocationOptions(): array
    {
        return Location::find()
            ->alias('location')
            ->select(['location.name', 'location.id'])
            ->innerJoin(
                ['country' => Country::tableName()],
                '[[country.id]] = [[location.country_id]]'
            )
            ->where(['country.code' => $this->executor->country_code])
            ->orderBy(['location.name' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public function getServiceOptions(): array
    {
        return ExecutorHelper::getServiceTypes();
    }
}
