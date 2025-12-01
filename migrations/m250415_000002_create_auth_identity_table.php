<?php

use app\core\Migration;

class m250415_000002_create_auth_identity_table extends Migration
{
    private string $tableName = '{{%auth_identity}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'type' => $this->tinyInteger()->notNull(),
            'identifier' => $this->string()->notNull(),
            'credential' => $this->string()->null(),

            'verified'  => $this->boolean()->notNull()->defaultValue(false),
            'verified_at'  => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
        ]);

        // idx
        $this->createIndex('idx-auth_identity-unique', $this->tableName, ['identifier', 'type'], true);

        // fk
        $this->addForeignKey(
            'fk-auth_identity-user',
            $this->tableName,
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}