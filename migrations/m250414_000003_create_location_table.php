<?php

use app\core\Migration;

/**
 * Handles the creation of table `{{%location}}` for MariaDB.
 */
class m250414_000003_create_location_table extends Migration
{
    public $tableName = '{{%location}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'country_id' => $this->integer()->notNull(),
            'parent_id' => $this->integer()->null(),
            'type' => $this->tinyInteger()->notNull(),
            'name' => $this->string(128)->notNull(),
            'search_keywords' => $this->text(),
            'extra_fields' => $this->json(),
            'polygon' => 'POLYGON NOT NULL',
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull()
        ]);

        // Foreign key
        $this->addForeignKey(
            'fk-location-country_id',
            $this->tableName,
            'country_id',
            '{{%country}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Self-referencing foreign key for parent region (optional)
        $this->addForeignKey(
            'fk-location-parent_id',
            $this->tableName,
            'parent_id',
            $this->tableName,
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Indexes
        $this->createIndex('idx-location-country_id', $this->tableName, 'country_id');
        $this->createIndex('idx-location-parent_id', $this->tableName, 'parent_id');
        $this->createIndex('idx-location-type', $this->tableName, 'type');

        // SPATIAL INDEX for geo-queries (ST_Contains)
        $this->execute("ALTER TABLE {$this->tableName} ADD SPATIAL INDEX idx_location_polygon (polygon)");
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}