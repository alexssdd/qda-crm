<?php

use app\core\Migration;


/**
 * Handles the creation of table `token`.
 */
class m250416_000001_create_token_table extends Migration
{
    public $tableName = '{{%token}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'token' => $this->string()->notNull()->unique(),
            'expired_at' => $this->integer()->notNull(),
        ]);

        // idx
        $this->createIndex('idx-token-user_id', '{{%token}}', 'user_id');

        // fk
        $this->addForeignKey('fk-token-user_id', '{{%token}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
