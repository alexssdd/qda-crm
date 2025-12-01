<?php

use app\core\Migration;

/**
 * Handles the creation of table `{{%config}}`.
 */
class m250001_000001_create_config_table extends Migration
{
    public $tableName = '{{%config}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'key' => $this->string()->notNull(),
            'type' => $this->integer(),
            'values' => $this->json(),
            'encrypted_values' => $this->json()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
