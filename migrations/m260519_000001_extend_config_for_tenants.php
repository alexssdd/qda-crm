<?php

use yii\db\Migration;

/**
 * Расширяет таблицу `{{%config}}` колонками `region` и `direction`.
 *
 * После миграции одна таблица обслуживает три класса конфигов:
 *  - region='global'/direction='global' — собственные конфиги глобала
 *    (mail, gpt, release.client.ios, release.pro.android и т.п.).
 *    Backfill существующих записей идёт сюда через DEFAULT.
 *  - region='kz'/direction='pro' (и аналоги) — конфиги конкретного
 *    api-инстанса. Секреты в encrypted_values зашифрованы ключом тенанта
 *    (`tenant.encryption.keys[region.direction]`), глобал их не расшифровывает.
 *  - region='*'/direction='*' — wildcard, применяется ко всем инстансам
 *    (используется при отдаче снимка тенанту: матчится по pair OR wildcard).
 *
 * Уникальный индекс на (key, region, direction) — для upsert по тенант-ключу.
 *
 * Локальная таблица config на api-инстансах (pro/api/kz, client/api/kz)
 * НЕ ТРОГАЕТСЯ — там тенант всегда один (свой) и колонки не нужны.
 */
class m260519_000001_extend_config_for_tenants extends Migration
{
    public $tableName = '{{%config}}';

    public function safeUp(): void
    {
        $this->addColumn($this->tableName, 'region', $this->string(8)->notNull()->defaultValue('global')->after('key'));
        $this->addColumn($this->tableName, 'direction', $this->string(16)->notNull()->defaultValue('global')->after('region'));

        $this->createIndex('uq-config-tenant-key', $this->tableName, ['key', 'region', 'direction'], true);

        // Отдельный индекс под `getTenantSnapshot` (WHERE region IN (?, *) AND direction IN (?, *)) —
        // unique-индекс не покрывает запрос без leading column `key`.
        $this->createIndex('idx-config-region-direction', $this->tableName, ['region', 'direction']);
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx-config-region-direction', $this->tableName);
        $this->dropIndex('uq-config-tenant-key', $this->tableName);
        $this->dropColumn($this->tableName, 'direction');
        $this->dropColumn($this->tableName, 'region');
    }
}
