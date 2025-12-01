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
            'code' => $this->string(32)->notNull()->unique(), // например: "kz", "global"
            'name' => $this->string(128)->notNull(), // Kazakhstan
            'client_api_url' => $this->string(255)->null(),
            'pro_api_url' => $this->string(255)->null(),
            'phone_code' => $this->string(8)->null(), // +7
            'phone_mask' => $this->string(32)->null(), // +7 (7##) ###-##-##
            'extra_fields' => $this->json(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}