<?php

use app\core\Migration;

class m260727_210000_alter_auth_otp_code_add_verify_attempts extends Migration
{
    private string $tableName = '{{%auth_otp_code}}';

    public function safeUp(): void
    {
        $this->addColumn(
            $this->tableName,
            'verify_attempts',
            $this->integer()->notNull()->defaultValue(0)->after('created_at')
        );
    }

    public function safeDown(): void
    {
        $this->dropColumn($this->tableName, 'verify_attempts');
    }
}
