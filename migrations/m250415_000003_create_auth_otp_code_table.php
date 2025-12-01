<?php

use app\core\Migration;

class m250415_000003_create_auth_otp_code_table extends Migration
{
    private string $tableName = '{{%auth_otp_code}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'identity_id' => $this->integer()->unsigned()->notNull(),
            'code_hash'   => $this->string()->notNull(),
            'expires_at'  => $this->integer()->notNull(),
            'created_at'  => $this->integer()->notNull(),
        ]);

        // pk
        $this->addPrimaryKey('pk-auth_otp_code', $this->tableName, 'identity_id');

        // idx
        $this->createIndex(
            'idx-auth_otp_code-expires_at',
            $this->tableName,
            'expires_at'
        );

        // fk
        $this->addForeignKey(
            'fk-auth_otp_code-identity',
            $this->tableName,
            'identity_id',
            '{{%auth_identity}}',
            'id',
            'CASCADE',
        );
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}