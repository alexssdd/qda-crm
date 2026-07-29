<?php

use app\core\Migration;

class m260728_070000_create_or_extend_customer_sync extends Migration
{
    private string $tableName = '{{%customer}}';

    public function safeUp(): void
    {
        if ($this->getTableSchema() === null) {
            $this->createLegacyCustomerTable();
        }

        $this->addColumnIfMissing('source_id', $this->integer()->unsigned()->null());
        $this->addColumnIfMissing('country_code', $this->string(5)->null());
        $this->addColumnIfMissing('registered_at', $this->integer()->null());
        $this->addColumnIfMissing('orders_created', $this->integer()->unsigned()->notNull()->defaultValue(0));
        $this->addColumnIfMissing('orders_completed', $this->integer()->unsigned()->notNull()->defaultValue(0));
        $this->addColumnIfMissing('orders_canceled', $this->integer()->unsigned()->notNull()->defaultValue(0));
        $this->addColumnIfMissing('last_order_at', $this->integer()->null());

        $this->createIndexIfMissing(
            'idx-customer-source',
            ['source_id', 'country_code'],
            true
        );
        $this->createIndexIfMissing('idx-customer-country_code', ['country_code']);
        $this->createIndexIfMissing('idx-customer-status', ['status']);
        $this->createIndexIfMissing('idx-customer-registered_at', ['registered_at']);
        $this->createIndexIfMissing('idx-customer-last_order_at', ['last_order_at']);
    }

    public function safeDown(): void
    {
        // Нереверсивно намеренно: таблица legacy могла существовать до Yii-миграций,
        // а отдельные sync-колонки — быть добавлены вручную. Удалять их и накопленные
        // данные при rollback этой миграции небезопасно.
    }

    private function createLegacyCustomerTable(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'parent_id' => $this->integer()->unsigned()->null(),
            'name' => $this->string()->notNull()->defaultValue(''),
            'phone' => $this->string(21)->null(),
            'email' => $this->string()->null(),
            'ref' => $this->string()->null(),
            'type' => $this->tinyInteger(2)->null(),
            'iin' => $this->string(20)->null(),
            'config' => $this->json()->null(),
            'status' => $this->tinyInteger(2)->notNull()->defaultValue(10),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
        ]);
    }

    private function addColumnIfMissing(string $name, $type): void
    {
        if (!$this->columnExists($name)) {
            $this->addColumn($this->tableName, $name, $type);
            $this->db->schema->refreshTableSchema($this->tableName);
        }
    }

    private function createIndexIfMissing(
        string $name,
        array $columns,
        bool $unique = false
    ): void {
        if (!$this->indexExists($name)) {
            $this->createIndex($name, $this->tableName, $columns, $unique);
        }
    }

    private function columnExists(string $name): bool
    {
        $schema = $this->getTableSchema();

        return $schema !== null && isset($schema->columns[$name]);
    }

    private function indexExists(string $name): bool
    {
        $tableName = $this->db->schema->getRawTableName($this->tableName);

        return (bool) $this->db->createCommand(
            'SELECT 1
               FROM information_schema.statistics
              WHERE table_schema = DATABASE()
                AND table_name = :table_name
                AND index_name = :index_name
              LIMIT 1',
            [
                ':table_name' => $tableName,
                ':index_name' => $name,
            ]
        )->queryScalar();
    }

    private function getTableSchema(): ?\yii\db\TableSchema
    {
        return $this->db->schema->getTableSchema($this->tableName, true);
    }
}
