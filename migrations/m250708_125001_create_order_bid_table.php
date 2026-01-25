<?php

use app\core\Migration;

class m250708_125001_create_order_bid_table extends Migration
{
    public $tableName = '{{%order_bid}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'source_id' => $this->integer()->unsigned()->notNull(),
            'country_code' => $this->string(5)->notNull(),

            'order_id' => $this->integer()->unsigned()->notNull(),
            'executor_id' => $this->integer()->unsigned()->notNull(),

            'price' => $this->decimal(10, 2)->notNull(),
            'comment' => $this->string(255)->null(),
            'is_executor' => $this->tinyInteger(1)->defaultValue(0),

            'source_at' => $this->integer()->unsigned()->notNull(),
            'status' => $this->tinyInteger(2)->notNull(),
        ]);

        // Idx
        $this->createIndex('idx-bid-unique', $this->tableName, ['source_id', 'country_code'], true);

        // Fk
        $this->addForeignKey('fk-bid-order', $this->tableName, 'order_id', '{{%order}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-bid-executor', $this->tableName, 'executor_id', '{{%executor}}', 'id', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}