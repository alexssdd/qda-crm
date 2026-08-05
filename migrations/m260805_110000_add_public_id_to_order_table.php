<?php
use yii\db\Migration;

/**
 * Adds public order identifier used in share links and external API.
 */
class m260805_110000_add_public_id_to_order_table extends Migration
{
    public $tableName = '{{%order}}';

    public function safeUp(): void
    {
        // ascii_bin: base62 регистрозависим, CI-коллация таблицы склеила бы aB и Ab.
        $this->addColumn($this->tableName, 'public_id', 'CHAR(22) CHARACTER SET ascii COLLATE ascii_bin NULL');
        $this->createIndex('idx-order-public_id', $this->tableName, 'public_id', true);
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx-order-public_id', $this->tableName);
        $this->dropColumn($this->tableName, 'public_id');
    }
}
