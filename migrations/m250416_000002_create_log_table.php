<?php

use app\core\Migration;

/**
 * Handles the creation of table `{{%log}}`.
 */
class m250416_000002_create_log_table extends Migration
{
    public $tableName = '{{%log}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'target' => $this->string()->notNull(),
            'data' => $this->json(),
            'created_at' => $this->integer()->notNull(),
            'runtime' => $this->decimal(10, 2),
            'status' => $this->integer(),
        ]);

        // idx
        $this->createIndex('idx-log-target', $this->tableName, 'target');
        $this->createIndex('idx-log-status', $this->tableName, 'status');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
