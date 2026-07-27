<?php

use app\core\Migration;

class m260728_040000_add_completed_at_to_order_table extends Migration
{
    private string $tableName = '{{%order}}';

    public function safeUp(): void
    {
        $this->addColumn(
            $this->tableName,
            'completed_at',
            $this->integer()->null()->after('created_at'),
        );
    }

    public function safeDown(): void
    {
        $this->dropColumn($this->tableName, 'completed_at');
    }
}
