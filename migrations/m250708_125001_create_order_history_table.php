<?php

use app\core\Migration;

/**
 * Handles the creation of table `{{%order_history}}`.
 */
class m250708_125001_create_order_history_table extends Migration
{
    public $tableName = '{{%order_history}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->unsigned()->notNull(),

            'status_before' => $this->integer(),
            'status_after' => $this->integer(),

            // services fields
            'created_at' => $this->integer(),
            'created_by' => $this->integer()->unsigned(),
        ]);

        // fk
        $this->addForeignKey('fk-order_history-order_id', $this->tableName, 'order_id', '{{%order}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-order_history-created_by', $this->tableName, 'created_by', '{{%user}}', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}
