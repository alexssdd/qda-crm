<?php
use yii\db\Migration;

/**
 * Handles the creation of table `{{%order}}` with full structure.
 */
class m250708_125000_create_order_table extends Migration
{
    public $tableName = '{{%order}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'channel' => $this->integer()->notNull(),
            'country_code' => $this->string()->notNull(),
            'source_id' => $this->integer()->unsigned(),
            'type' => $this->tinyInteger(2)->notNull(),
            'phone' => $this->string(21)->notNull(),
            'name' => $this->string()->notNull(),
            'rating' => $this->decimal(3,2),

            // From
            'from_location_id' => $this->integer()->null(),
            'from_name' => $this->string(255),
            'from_address' => $this->string(255),
            'from_lat' => $this->decimal(10,7)->null(),
            'from_lng' => $this->decimal(10,7)->null(),

            // To
            'to_location_id' => $this->integer()->null(),
            'to_name' => $this->string(255),
            'to_address' => $this->string(255),
            'to_lat' => $this->decimal(10,7),
            'to_lng' => $this->decimal(10,7),

            'category' => $this->tinyInteger()->notNull(),
            'price_type' => $this->tinyInteger(2)->notNull(),
            'price' => $this->decimal(10,2)->null(),
            'payment_method' => $this->tinyInteger(2)->notNull(),

            'assignments' => $this->json()->null(),
            'extra_fields' => $this->json()->null(),

            'comment' => $this->string(255)->null(),
            'source_at' => $this->integer(),
            'created_at' => $this->integer()->notNull(),
            'status' => $this->tinyInteger(2)->notNull(),
        ]);

        // idx
        $this->createIndex('idx-order-channel', $this->tableName, 'channel');
        $this->createIndex('idx-source', $this->tableName, ['country_code', 'source_id'], true);
        $this->createIndex('idx-order-type', $this->tableName, 'type');
        $this->createIndex('idx-order-category', $this->tableName, 'category');
        $this->createIndex('idx-order-status', $this->tableName, 'status');

        // fk
        $this->addForeignKey('fk-order-from_location_id', $this->tableName, 'from_location_id', '{{%location}}', 'id', 'SET NULL', 'RESTRICT');
        $this->addForeignKey('fk-order-to_location_id', $this->tableName, 'to_location_id', '{{%location}}', 'id', 'SET NULL', 'RESTRICT');
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}