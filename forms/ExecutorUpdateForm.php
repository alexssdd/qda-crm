<?php

namespace app\forms;

use Yii;
use yii\base\Model;
use app\modules\location\models\Country;
use app\modules\location\models\Location;
use app\modules\location\helpers\RegionHelper;
use app\modules\location\helpers\LocationHelper;
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
        $locations = Location::find()
            ->alias('location')
            ->select([
                'location.id',
                'location.parent_id',
                'location.type',
                'location.name',
                'location.extra_fields',
            ])
            ->innerJoin(
                ['country' => Country::tableName()],
                '[[country.id]] = [[location.country_id]]'
            )
            ->where(['country.code' => $this->executor->country_code])
            ->orderBy(['location.name' => SORT_ASC])
            ->asArray()
            ->all();

        $regions = [];
        $children = [];
        $other = [];

        foreach ($locations as $location) {
            $id = (int) $location['id'];
            $parentId = (int) ($location['parent_id'] ?? 0);

            if ((int) $location['type'] === RegionHelper::TYPE_REGION) {
                $regions[$id] = $location;
            } elseif ($parentId > 0) {
                $children[$parentId][$id] = $this->getLocationOptionLabel($location);
            } else {
                $other[$id] = $this->getLocationOptionLabel($location);
            }
        }

        $options = [];

        foreach ($regions as $id => $region) {
            $items = [
                $id => $this->getLocationOptionLabel($region),
            ];

            if (isset($children[$id])) {
                $items += $children[$id];
                unset($children[$id]);
            }

            $options[LocationHelper::getName(
                $region['extra_fields'] ?? null,
                (string) $region['name']
            )] = $items;
        }

        foreach ($children as $items) {
            $other += $items;
        }

        if ($other !== []) {
            $options['Другие локации'] = $other;
        }

        return $options;
    }

    public function getServiceOptions(): array
    {
        return ExecutorHelper::getServiceTypes();
    }

    private function getLocationOptionLabel(array $location): string
    {
        $type = match ((int) $location['type']) {
            RegionHelper::TYPE_REGION => 'область',
            RegionHelper::TYPE_CITY => 'город',
            RegionHelper::TYPE_DISTRICT => 'район',
            default => null,
        };

        return LocationHelper::getName(
            $location['extra_fields'] ?? null,
            (string) $location['name']
        )
            . ($type === null ? '' : ' · ' . $type);
    }
}
