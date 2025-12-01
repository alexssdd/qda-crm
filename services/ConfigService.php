<?php

namespace app\services;

use Yii;
use DomainException;
use app\entities\Config;
use yii\caching\CacheInterface;
use app\core\helpers\CryptoHelper;

class ConfigService
{
    private const CACHE_TTL = 3600;

    private ?CacheInterface $cache;
    private string $encryptionKey;

    public function __construct()
    {
        $this->cache = Yii::$app->cache;
        $this->encryptionKey = Yii::$app->params['configEncryptionKey'];
    }

    public function get(string $group, string $key, $default = null)
    {
        $all = $this->getGroup($group);
        return $all[$key] ?? $default;
    }

    public function getRequired(string $group, string $key)
    {
        $value = $this->get($group, $key, null);

        if ($value === null || $value === '') {
            throw new DomainException("Config value '{$group}.{$key}' is not set.");
        }

        return $value;
    }

    public function getGroup(string $group): array
    {
        $cacheKey = "config:{$group}";

        return $this->cache->getOrSet($cacheKey, function () use ($group) {
            $model = Config::findOne(['key' => $group]);

            if (!$model) {
                return [];
            }

            $data = $model->values ?? [];

            if ($model->encrypted_values) {
                foreach ($model->encrypted_values as $k => $v) {
                    $data[$k] = CryptoHelper::decrypt($v, $this->encryptionKey);
                }
            }

            return $data;
        }, self::CACHE_TTL);
    }

    public function clear(string $group): void
    {
        $this->cache->delete("config:{$group}");
    }
}