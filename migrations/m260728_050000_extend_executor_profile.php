<?php

use app\core\Migration;

class m260728_050000_extend_executor_profile extends Migration
{
    private string $executorTable = '{{%executor}}';
    private string $serviceTable = '{{%executor_service}}';

    public function safeUp(): void
    {
        $this->renameColumn($this->executorTable, 'source_at', 'registered_at');
        $this->addColumn(
            $this->executorTable,
            'location_id',
            $this->integer()->null()->after('country_code')
        );

        $this->createIndex('idx-executor-location_id', $this->executorTable, 'location_id');
        $this->createIndex('idx-executor-registered_at', $this->executorTable, 'registered_at');
        $this->addForeignKey(
            'fk-executor-location_id',
            $this->executorTable,
            'location_id',
            '{{%location}}',
            'id',
            'SET NULL',
            'RESTRICT'
        );

        $this->createTable($this->serviceTable, [
            'id' => $this->primaryKey()->unsigned(),
            'executor_id' => $this->integer()->unsigned()->notNull(),
            'type' => $this->tinyInteger(2)->notNull(),
            'categories' => $this->json()->null(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-executor_service-executor-type',
            $this->serviceTable,
            ['executor_id', 'type'],
            true
        );
        $this->createIndex('idx-executor_service-type', $this->serviceTable, 'type');
        $this->addForeignKey(
            'fk-executor_service-executor_id',
            $this->serviceTable,
            'executor_id',
            $this->executorTable,
            'id',
            'CASCADE',
            'RESTRICT'
        );
    }

    public function safeDown(): void
    {
        $this->dropTable($this->serviceTable);

        $this->dropForeignKey('fk-executor-location_id', $this->executorTable);
        $this->dropIndex('idx-executor-registered_at', $this->executorTable);
        $this->dropIndex('idx-executor-location_id', $this->executorTable);
        $this->dropColumn($this->executorTable, 'location_id');
        $this->renameColumn($this->executorTable, 'registered_at', 'source_at');
    }
}
