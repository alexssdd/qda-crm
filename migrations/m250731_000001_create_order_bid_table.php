<?php

use app\core\Migration;

class m250731_000001_create_order_bid_table extends Migration
{
    public $tableName = '{{%order_bid}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'owner_id' => $this->integer()->unsigned()->notNull(),
            'order_id' => $this->integer()->unsigned()->notNull(),
            'phone' => $this->string(20)->notNull(),
            'region' => $this->string(5)->notNull(),
            'name' => $this->string(255)->notNull(),
            'rating' => $this->decimal(3,2),
            'price' => $this->decimal(10,2),

            'extra_fields' => $this->json()->null(),
            'comment' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'accepted_at' => $this->integer(),
            'status' => $this->tinyInteger(2)->notNull(),
        ]);

        // idx
        $this->createIndex('idx-order_bid-unique', $this->tableName, ['owner_id', 'region', 'order_id'], true);
        $this->createIndex('idx-order_bid-order-status-id', $this->tableName, ['order_id', 'status', 'id']);
        $this->createIndex('idx-order_bid-status', $this->tableName, 'status');

        // fk
        $this->addForeignKey(
            'fk-order_bid-order_id',
            $this->tableName,
            'order_id',
            '{{%order}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}