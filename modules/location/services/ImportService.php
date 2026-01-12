<?php

namespace app\modules\location\services;

use Yii;
use DomainException;
use yii\db\Expression;
use app\modules\location\models\Country;
use app\modules\location\models\Location;
use app\modules\location\helpers\RegionHelper;
use app\modules\location\helpers\CountryHelper;

class ImportService
{
    public function countries(): void
    {
        $countries = CountryHelper::getData();

        foreach ($countries as $item) {
            if (Country::find()->andWhere(['code' => $item['code']])->exists()) {
                continue;
            }

            $model = new Country();
            $model->code = $item['code'];
            $model->name = $item['name'];
            $model->client_api_url = $item['client_api_url'];
            $model->pro_api_url = $item['pro_api_url'];
            $model->phone_code = $item['phone_code'];
            $model->phone_mask = $item['phone_mask'];

            $model->status = CountryHelper::STATUS_ACTIVE;
            $model->updated_at = time();
            $model->created_at = time();

            if (!$model->save(false)) {
                throw new DomainException("Country create error");
            }
        }
    }

    public function locations($countryCode): void
    {
        $country = Country::findOne(['code' => $countryCode]);
        if (!$country) {
            throw new DomainException("❌ Страна '{$countryCode}' не найдена в базе");
        }

        $root = Yii::getAlias("@app/modules/location/storage/geojson/{$countryCode}");

        if (!is_dir($root)) {
            throw new DomainException("❌ Папка страны {$countryCode} не найдена: $root");
        }

        $count = 0;

        foreach (scandir($root) as $regionDir) {
            if ($regionDir === '.' || $regionDir === '..') {
                continue;
            }

            $regionPath = "{$root}/{$regionDir}";
            if (!is_dir($regionPath)) {
                continue;
            }

            // === Импорт региона ===
            $geoRegionDir = "{$regionPath}/region";
            foreach (glob("$geoRegionDir/*.geojson") as $file) {
                $location = $this->importLocationFile(
                    $country,
                    null,
                    $file,
                    RegionHelper::TYPE_REGION
                );

                if ($location) {
                    $count++;
                }
            }

            // === Импорт районов ===
            $geoDistrictDir = "{$regionPath}/district";
            $regionModel = $this->findRegionByFolder($country->id, $regionDir);

            if ($regionModel) {
                foreach (glob("$geoDistrictDir/*.geojson") as $file) {
                    if ($this->importLocationFile($country, $regionModel, $file, RegionHelper::TYPE_DISTRICT)) {
                        $count++;
                    }
                }
            }

            // === Импорт городов ===
            $geoCityDir = "{$regionPath}/city";

            if ($regionModel) {
                foreach (glob("$geoCityDir/*.geojson") as $file) {
                    if ($this->importLocationFile($country, $regionModel, $file, RegionHelper::TYPE_CITY)) {
                        $count++;
                    }
                }
            }
        }
    }

    public function structure(): void
    {
        $locations = CountryHelper::getData();

        foreach ($locations as $location) {
            $countryCode = $location['code'];

            $baseDir = Yii::getAlias('@app/modules/location/storage/geojson') . "/$countryCode";
            $jsonFile = $baseDir . "/structure.json";

            if (!file_exists($jsonFile)) {
                throw new DomainException("⚠️ Пропускаю {$countryCode}: нет файла структуры — $jsonFile\n");
                // continue;
            }

            $data = json_decode(file_get_contents($jsonFile), true);

            if (!isset($data[$countryCode])) {
                throw new DomainException("❌ Неверная структура в $jsonFile");
                // continue;
            }

            if (!is_dir($baseDir)) {
                mkdir($baseDir, 0755, true);
            }

            foreach ($data[$countryCode] as $regionKey => $regionData) {
                $regionDir = "$baseDir/$regionKey";
                $dirs = [
                    'region'   => "$regionDir/region",
                    'district' => "$regionDir/district",
                    'city'     => "$regionDir/city",
                ];

                foreach ($dirs as $dir) {
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                }

                // 1. REGION
                $regionGeo = [
                    'slug'     => $regionKey,
                    'names'    => $regionData['names'] ?? [],
                    'keywords' => $regionData['keywords'] ?? [],
                    'type'     => 'region',
                ];

                $regionPath = "{$dirs['region']}/{$regionKey}.geojson";
                $this->writeGeoJson($regionPath, $regionGeo);

                // 2. DISTRICTS
                foreach ($regionData['districts'] ?? [] as $district) {
                    $path = "{$dirs['district']}/{$district['slug']}.geojson";
                    $this->writeGeoJson($path, array_merge($district, ['type' => 'district']));
                }

                // 3. CITIES
                foreach ($regionData['cities'] ?? [] as $city) {
                    $path = "{$dirs['city']}/{$city['slug']}.geojson";
                    $this->writeGeoJson($path, array_merge($city, ['type' => 'city']));
                }
            }
        }
    }

    private function importLocationFile(Country $country, ?Location $parent, string $file, int $type): ?Location
    {
        $json = json_decode(file_get_contents($file), true);

        if (!isset($json['features'][0]['geometry'])) {
            throw new DomainException("❌ Нет geometry: $file");
        }

        $basename = pathinfo($file, PATHINFO_FILENAME);
        $name = ucwords(str_replace(['_', '-'], ' ', $basename));

        $geometry = $json['features'][0]['geometry'];
        $wkt = $this->geometryToWkt($geometry);

        if (!$wkt) {
            throw new DomainException("❌ Ошибка WKT: {$name}");
        }

        $meta = $json['metadata'] ?? [];
        $names = $meta['names'] ?? [];
        $keywords = $meta['search_keywords'] ?? [];

        if (!$location = Location::find()
            ->andWhere([
                'country_id' => $country->id,
                'type' => $type,
                'parent_id' => $parent?->id,
                'name' => $name
            ])
            ->one()
        ) {
            $location = new Location();
            $location->country_id = $country->id;
            $location->parent_id = $parent?->id;
            $location->type = $type;
            $location->name = $name;
            $location->created_at = time();
        }

        $extraFields = $location->extra_fields;
        $extraFields['names'] = $names;
        $location->extra_fields = $extraFields;
        $location->search_keywords = $keywords ? implode(',', $keywords) : null;
        $location->updated_at = time();
        $location->polygon = new Expression("ST_GeomFromText(:wkt)", [':wkt' => $wkt]);

        if (!$location->save(false)) {
            throw new DomainException("✅ Сохранено: {$name}");
        }

        return $location;
    }

    private function findRegionByFolder(int $countryId, string $folder): ?Location
    {
        $name = ucwords(str_replace(['_', '-'], ' ', $folder));

        return Location::find()
            ->where([
                'country_id' => $countryId,
                'type' => RegionHelper::TYPE_REGION,
                'name' => $name
            ])
            ->one();
    }

    private function writeGeoJson(string $path, array $meta): void
    {
        // Был ли уже файл?
        $existing = file_exists($path)
            ? json_decode(file_get_contents($path), true)
            : null;

        // Извлекаем существующие coordinates
        $coordinates = $existing['features'][0]['geometry']['coordinates'] ?? [];

        // Извлекаем существующие keywords
        $existingKeywords = [];
        if (isset($existing['metadata']['search_keywords']) && is_array($existing['metadata']['search_keywords'])) {
            $existingKeywords = $existing['metadata']['search_keywords'];
        }

        // Новые слова из структуры
        $newKeywords = $meta['keywords'] ?? [];

        // Объединяем и фильтруем дубликаты
        $mergedKeywords = array_values(array_unique(array_merge($existingKeywords, $newKeywords)));

        // Собираем финальный GeoJSON
        $geojson = [
            'type' => 'FeatureCollection',
            'metadata' => [
                'slug' => $meta['slug'],
                'type' => $meta['type'],
                'names' => $meta['names'] ?? [],
                'search_keywords' => $mergedKeywords,
            ],
            'features' => [
                [
                    'type' => 'Feature',
                    'id' => 0,
                    'geometry' => [
                        'type' => 'Polygon',
                        'coordinates' => $coordinates
                    ],
                    'properties' => [
                        "fill" => "#ed4543",
                        "fill-opacity" => 0.6,
                        "stroke" => "#ed4543",
                        "stroke-width" => "5",
                        "stroke-opacity" => 0.9
                    ]
                ]
            ]
        ];

        // Пишем красиво
        $json = json_encode(
            $geojson,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        file_put_contents($path, $json);
    }

    private function geometryToWkt(array $geometry): ?string
    {
        if (!$geometry['coordinates']) {
            return null;
        }
        return match ($geometry['type']) {
            'Polygon' => $this->formatPolygon($geometry['coordinates']),
            'MultiPolygon' => $this->formatMultiPolygon($geometry['coordinates']),
            default => null,
        };
    }

    private function formatPolygon(array $rings): string
    {
        $parts = [];
        foreach ($rings as $ring) {
            $parts[] = '(' . implode(',', array_map(fn($c) => "{$c[0]} {$c[1]}", $ring)) . ')';
        }
        return 'POLYGON(' . implode(',', $parts) . ')';
    }

    private function formatMultiPolygon(array $multi): string
    {
        $polygons = [];
        foreach ($multi as $polygon) {
            $rings = [];
            foreach ($polygon as $ring) {
                $rings[] = '(' . implode(',', array_map(fn($c) => "{$c[0]} {$c[1]}", $ring)) . ')';
            }
            $polygons[] = '(' . implode(',', $rings) . ')';
        }
        return 'MULTIPOLYGON(' . implode(',', $polygons) . ')';
    }
}