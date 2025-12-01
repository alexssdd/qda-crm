<?php

use app\core\Migration;

class m250415_000001_create_user_table extends Migration
{
    private string $tableName = '{{%user}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'role' => $this->string(20)->notNull(),
            'phone' => $this->string(21)->notNull(),
            'country' => $this->string(3)->notNull(),
            'name' => $this->string()->notNull(),
            'status' => $this->tinyInteger()->notNull(),
            'created_at' => $this->integer()->notNull()
        ]);

        // idx
        $this->createIndex('idx-user-role', $this->tableName, 'role');
        $this->createIndex('idx-user-phone-unique', $this->tableName, 'phone', true);
        $this->createIndex('idx-user-country', $this->tableName, 'country');
        $this->createIndex('idx-user-status', $this->tableName, 'status');
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}