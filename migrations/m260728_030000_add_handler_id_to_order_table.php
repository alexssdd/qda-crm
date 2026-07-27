<?php

use app\core\Migration;

class m260728_030000_add_handler_id_to_order_table extends Migration
{
    private string $tableName = '{{%order}}';

    public function safeUp(): void
    {
        $this->addColumn(
            $this->tableName,
            'handler_id',
            $this->integer()->unsigned()->null()->after('assignments'),
        );

        $this->createIndex(
            'idx-order-handler_id',
            $this->tableName,
            'handler_id',
        );

        $this->addForeignKey(
            'fk-order-handler_id',
            $this->tableName,
            'handler_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'RESTRICT',
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-order-handler_id', $this->tableName);
        $this->dropIndex('idx-order-handler_id', $this->tableName);
        $this->dropColumn($this->tableName, 'handler_id');
    }
}
