<?php

namespace app\modules\location\services;


use DomainException;
use app\modules\location\models\Location;

class LocationService
{
    public function create($countryId, $name, $type, $polygon, $parentId = null): void
    {
        $location = new Location();
        $location->country_id = $countryId;
        $location->parent_id = $parentId;
        $location->type = $type;
        $location->name = $name;
        $location->created_at = time();
        $location->updated_at = time();
        $location->polygon = new \yii\db\Expression(
            "ST_GeomFromText(:wkt)",
            [':wkt' => $polygon]
        );

        if (!$location->save(false)) {
            throw new DomainException("Unable create location");
        }
    }
}