<?php

namespace app\core\security;

use Yii;
use DomainException;
use yii\helpers\Json;
use app\forms\cart\CartCreateForm;
use app\forms\cart\CartCalcDeliveryForm;
use yii\base\InvalidConfigException;

/**
 * Signs delivery calculation results so price and store cannot be changed in the browser.
 */
final class DeliveryQuote
{
    private const VERSION = 1;
    private const TTL = 600;
    private const MAX_CLOCK_SKEW = 30;

    public static function issue(CartCalcDeliveryForm $form, array $result): string
    {
        $payload = [
            'version' => self::VERSION,
            'issued_at' => time(),
            'expires_at' => time() + self::TTL,
            'merchant_id' => (int) $form->merchant_id,
            'city_id' => (int) $form->city_id,
            'lat' => self::normalizeNumber($form->lat),
            'lng' => self::normalizeNumber($form->lng),
            'products' => self::normalizeCalculatedProducts($form->products),
            'store_id' => (int) $result['store_id'],
            'cost' => (float) $result['cost'],
        ];

        $data = Json::encode($payload);

        return base64_encode(Yii::$app->security->hashData($data, self::getSigningKey()));
    }

    /**
     * @return array{store_id: int, cost: float}
     */
    public static function verify(CartCreateForm $form): array
    {
        $signedData = base64_decode((string) $form->delivery_quote, true);
        if ($signedData === false) {
            throw new DomainException('Расчет доставки поврежден. Рассчитайте доставку повторно.');
        }

        $data = Yii::$app->security->validateData($signedData, self::getSigningKey());
        if ($data === false) {
            throw new DomainException('Расчет доставки был изменен. Рассчитайте доставку повторно.');
        }

        try {
            $payload = Json::decode($data);
        } catch (\Throwable $e) {
            throw new DomainException('Некорректный расчет доставки.');
        }

        if (
            !is_array($payload)
            || ($payload['version'] ?? null) !== self::VERSION
            || !is_int($payload['issued_at'] ?? null)
            || !is_int($payload['expires_at'] ?? null)
            || ($payload['issued_at'] - self::MAX_CLOCK_SKEW) > time()
            || $payload['expires_at'] < time()
            || $payload['expires_at'] > ($payload['issued_at'] + self::TTL)
            || !is_int($payload['store_id'] ?? null)
            || $payload['store_id'] <= 0
            || !is_numeric($payload['cost'] ?? null)
            || (float) $payload['cost'] < 0
        ) {
            throw new DomainException('Расчет доставки устарел. Рассчитайте доставку повторно.');
        }

        $expected = [
            'merchant_id' => (int) $form->merchant_id,
            'city_id' => (int) $form->city_id,
            'lat' => self::normalizeNumber($form->lat),
            'lng' => self::normalizeNumber($form->lng),
            'products' => self::normalizeCreatedProducts($form->products),
        ];

        foreach ($expected as $key => $value) {
            if (($payload[$key] ?? null) !== $value) {
                throw new DomainException('Данные заказа изменились. Рассчитайте доставку повторно.');
            }
        }

        return [
            'store_id' => $payload['store_id'],
            'cost' => (float) $payload['cost'],
        ];
    }

    private static function normalizeCalculatedProducts(array $products): array
    {
        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'id' => (int) $product['id'],
                'quantity' => self::normalizeNumber($product['quantity']),
            ];
        }

        usort($result, static fn(array $left, array $right): int => $left['id'] <=> $right['id']);

        return $result;
    }

    private static function normalizeCreatedProducts(array $products): array
    {
        $result = [];
        foreach ($products as $id => $product) {
            $result[] = [
                'id' => (int) $id,
                'quantity' => self::normalizeNumber($product['quantity']),
            ];
        }

        usort($result, static fn(array $left, array $right): int => $left['id'] <=> $right['id']);

        return $result;
    }

    private static function normalizeNumber($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 6, '.', ''), '0'), '.');
    }

    private static function getSigningKey(): string
    {
        $key = (string) Yii::$app->request->cookieValidationKey;
        if ($key === '') {
            throw new InvalidConfigException('Request cookieValidationKey must be configured.');
        }

        return $key;
    }
}
