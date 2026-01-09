<?php

use app\core\Migration;

/**
 * Handles the creation of table `{{%country}}`.
 */
class m250001_000002_create_country_table extends Migration
{
    public $tableName = '{{%country}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'code' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(128)->notNull(),
            'client_api_url' => $this->string(255)->null(),
            'pro_api_url' => $this->string(255)->null(),
            'phone_code' => $this->string(8)->null(),
            'phone_mask' => $this->string(32)->null(),
            'extra_fields' => $this->json(),

            'sort' => $this->integer()->defaultValue(100),
            'status' => $this->smallInteger()->notNull(),

            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->null(),
        ]);

        // idx
        $this->createIndex('{{%idx-country-status}}', $this->tableName, 'status');
        $this->createIndex('{{%idx-country-sort}}', $this->tableName, 'sort');
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}