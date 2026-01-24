<?php

use app\core\Migration;

/**
 * Handles the creation of table `{{%order_event}}`.
 */
class m250708_125002_create_order_event_table extends Migration
{
    public $tableName = '{{%order_event}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),

            'order_id' => $this->integer()->notNull()->unsigned(),
            'history_id' => $this->integer()->notNull(),

            'type' => $this->string(),
            'message' => $this->text(),
            'data' => $this->json(),

            'created_at' => $this->integer(),
            'created_by' => $this->integer()->unsigned(),
        ]);

        // idx
        $this->createIndex('idx-order_event-type', $this->tableName, 'type');

        // fk
        $this->addForeignKey('fk-order_event-order_id', $this->tableName, 'order_id', '{{%order}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-order_event-history_id', $this->tableName, 'history_id', '{{%order_history}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-order_event-created_by', $this->tableName, 'created_by', '{{%user}}', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
