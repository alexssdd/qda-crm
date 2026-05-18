<?php

use app\core\Migration;

class m250001_000001_create_config_table extends Migration
{
    public $tableName = '{{%config}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'key' => $this->string()->notNull(),
            'region' => $this->string(8)->notNull()->defaultValue('global'),
            'direction' => $this->string(16)->notNull()->defaultValue('global'),
            'type' => $this->integer(),
            'values' => $this->json(),
            'encrypted_values' => $this->json(),
            'previews' => $this->json()->null(),
        ]);

        $this->createIndex('uq-config-tenant-key', $this->tableName, ['key', 'region', 'direction'], true);
        $this->createIndex('idx-config-region-direction', $this->tableName, ['region', 'direction']);
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}
