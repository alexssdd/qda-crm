<?php
use app\core\Migration;

class m250415_170000_create_auth_session_table extends Migration
{
    private string $tableName = '{{%auth_session}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'session_id' => $this->char(36)->notNull()->unique(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'refresh_hash' => $this->char(64)->notNull(),
            'device' => $this->string(60)->null(),
            'ip' => $this->string(45)->null(),
            'created_at' => $this->integer()->notNull(),
            'expires_at' => $this->integer()->notNull(),
            'revoked_at' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-auth_session-user_id', $this->tableName, 'user_id');
        $this->createIndex('idx-auth_session-refresh_hash', $this->tableName, 'refresh_hash', true);
        $this->createIndex('idx-auth_session-expires_at', $this->tableName, 'expires_at');

        $this->addForeignKey(
            'fk-auth_session-user_id',
            $this->tableName,
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}