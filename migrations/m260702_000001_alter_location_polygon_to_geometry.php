<?php

use app\core\Migration;

/**
 * ow_location.polygon: POLYGON → GEOMETRY.
 *
 * Составные территории (город с эксклавами, район, разрезанный анклавом
 * города) хранятся как MULTIPOLYGON — тип POLYGON их не принимает и импорт
 * падал. GEOMETRY — надмножество: существующие POLYGON-значения валидны,
 * ST_Contains/ST_AsGeoJSON работают без изменений, SPATIAL-индекс сохраняется.
 */
class m260702_000001_alter_location_polygon_to_geometry extends Migration
{
    public function safeUp(): void
    {
        $this->execute("ALTER TABLE {{%location}} MODIFY polygon GEOMETRY NOT NULL");
    }

    public function safeDown(): void
    {
        // откат возможен только пока в таблице нет MULTIPOLYGON-значений
        $this->execute("ALTER TABLE {{%location}} MODIFY polygon POLYGON NOT NULL");
    }
}
