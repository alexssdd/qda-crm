<?php

use yii\db\Migration;

/**
 * Добавляет колонку `previews JSON` в таблицу `{{%config}}`.
 *
 * Хранит безопасные «маски» для значений из `encrypted_values` (например
 * '•••xyz4' для строкового секрета) — для отображения в admin-UI без
 * расшифровки. Глобал не имеет операции decrypt чужих секретов;
 * preview вычисляется в ConfigService::set из plain в момент записи
 * и хранится отдельно от ciphertext.
 *
 * previews — Map<key, string|null>; набор ключей соответствует
 * encrypted_values; для значений в `values` (plain) preview не нужен.
 *
 * Колонка не отдаётся api-инстансам в getTenantSnapshot — там есть plain
 * после расшифровки своим ключом, маска не нужна.
 */
class m260519_000001_add_previews_to_config extends Migration
{
    public $tableName = '{{%config}}';

    public function safeUp(): void
    {
        $this->addColumn($this->tableName, 'previews', $this->json()->null()->after('encrypted_values'));
    }

    public function safeDown(): void
    {
        $this->dropColumn($this->tableName, 'previews');
    }
}
