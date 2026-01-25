<?php

use app\core\Migration;

class m250707_000001_create_executor_table extends Migration
{
    public $tableName = '{{%executor}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'source_id' => $this->integer()->unsigned()->notNull(),
            'country_code' => $this->string(5)->notNull(),

            'phone' => $this->string(20)->notNull(),
            'name' => $this->string(255)->notNull(),
            'rating' => $this->decimal(3, 2)->defaultValue(0),
            'is_verified' => $this->tinyInteger(1)->defaultValue(0),

            'orders_completed' => $this->integer()->unsigned()->defaultValue(0),
            'orders_canceled' => $this->integer()->unsigned()->defaultValue(0),

            'source_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'status' => $this->tinyInteger(2)->notNull(),
        ]);

        $this->createIndex('idx-executor-source', $this->tableName, ['source_id', 'country_code'], true);
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}