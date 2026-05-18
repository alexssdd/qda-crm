<?php

use app\core\Migration;

/**
 * Создаёт таблицу `{{%config}}` для конфигурации global API и api-инстансов.
 *
 * Одна таблица обслуживает три класса конфигов через колонки (region, direction):
 *  - region='global'/direction='global' — собственные конфиги глобала
 *    (mail, release.client.ios, release.pro.android и т.п.). Секреты в
 *    encrypted_values шифруются `params['configEncryptionKey']` глобала.
 *  - region='kz'/direction='pro' (и аналоги) — конфиги конкретного api-инстанса.
 *    Секреты шифруются ключом тенанта `params['tenant.encryption.keys'][region.direction]`,
 *    глобал их не расшифровывает — отдаёт ciphertext инстансу через /v1/instance/config.
 *  - region='*'/direction='*' — wildcard, применяется ко всем инстансам
 *    (используется при отдаче снимка тенанту: матчится по pair OR wildcard).
 *
 * Индексы:
 *  - UNIQUE (key, region, direction) — для upsert по тенант-ключу
 *  - (region, direction) — под getTenantSnapshot, который фильтрует
 *    по (region IN (?, *) AND direction IN (?, *)) без leading column `key`
 *
 * Локальная таблица config на api-инстансах (pro/api/kz, client/api/kz)
 * НЕ имеет колонок region/direction — там тенант всегда один (свой).
 */
class m250001_000001_create_config_table extends Migration
{
    public $tableName = '{{%config}}';

    public function safeUp(): void
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'key' => $this->string()->notNull(),
            'region' => $this->string(8)->notNull()->defaultValue('global'),
            'direction' => $this->string(16)->notNull()->defaultValue('global'),
            'type' => $this->integer(),
            'values' => $this->json(),
            'encrypted_values' => $this->json(),
        ]);

        $this->createIndex('uq-config-tenant-key', $this->tableName, ['key', 'region', 'direction'], true);
        $this->createIndex('idx-config-region-direction', $this->tableName, ['region', 'direction']);
    }

    public function safeDown(): void
    {
        $this->dropTable($this->tableName);
    }
}
