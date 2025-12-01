<?php

namespace app\core\helpers;

use Yii;
use Exception;
use app\entities\Lead;
use app\entities\Order;
use yii\helpers\ArrayHelper;
use app\core\hash\HashCrypto;
use app\entities\OrderProduct;
use app\modules\wb\helpers\WbHelper;
use app\modules\ozon\helper\OzonHelper;

/**
 * Order helper
 */
class OrderHelper
{
    /** Accounts */
    const ACCOUNT_MHV = 1;
    const ACCOUNT_PTW = 2;

    // Processed
    const STATUS_NEW = 10;
    const STATUS_ACCEPTED = 11;

    // Delivery
    const STATUS_SHIPPED = 12;
    const STATUS_COURIER = 13;
    const STATUS_DELIVERED = 14;

    // Pickup
    const STATUS_PICKUP = 15;
    const STATUS_ISSUED = 16;

    // Canceled
    const STATUS_CANCELLED = 17;

    /** Channels */
    const CHANNEL_CRM = 10;
    const CHANNEL_MARKET = 11;
    const CHANNEL_KASPI_SHOP = 12;
    const CHANNEL_HALYK_MARKET = 13;
    const CHANNEL_WOLT = 14;
    const CHANNEL_GLOVO = 15;
    const CHANNEL_YE = 16;
    const CHANNEL_JUSAN = 17;
    const CHANNEL_FORTE = 18;
    const CHANNEL_BNPL = 19;
    const CHANNEL_SITE_MARWIN = 20;
    const CHANNEL_SITE_MELOMAN = 21;
    const CHANNEL_OZON = 22;
    const CHANNEL_WB = 23;
    const CHANNEL_APP_IOS = 24;
    const CHANNEL_APP_ANDROID = 25;
    const CHANNEL_CERTIFICATE = 26;

    /** Delivery */
    const DELIVERY_COST_FREE = 0;

    /**
     * @param Order $order
     * @return bool
     */
    public static function isChannelSite(Order $order): bool
    {
        return in_array($order->channel, [self::CHANNEL_SITE_MARWIN, self::CHANNEL_SITE_MELOMAN]);
    }

    /**
     * @param Order $order
     * @return bool
     */
    public static function isChannelMobile(Order $order): bool
    {
        return in_array($order->channel, [self::CHANNEL_APP_IOS, self::CHANNEL_APP_ANDROID]);
    }

    /**
     * @return string[]
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => 'Новый',
            self::STATUS_ACCEPTED => 'Принят',
            self::STATUS_SHIPPED => 'На доставку',
            self::STATUS_COURIER => 'Курьер принял',
            self::STATUS_PICKUP => 'Самовывоз',
            self::STATUS_DELIVERED => 'Доставлен',
            self::STATUS_ISSUED => 'Выдан клиенту',
            self::STATUS_CANCELLED => 'Отменен',
        ];
    }

    /**
     * @param $status
     * @return string|null
     * @throws Exception
     */
    public static function getStatusName($status): ?string
    {
        return ArrayHelper::getValue(self::getStatuses(), $status);
    }

    /**
     * @return string[]
     */
    public static function getChannels(): array
    {
        return [
            self::CHANNEL_CRM => 'CRM',
            self::CHANNEL_MARKET => 'Market',
            self::CHANNEL_KASPI_SHOP => 'Kaspi Shop',
            self::CHANNEL_HALYK_MARKET => 'Halyk Market',
            self::CHANNEL_WOLT => 'Wolt',
            self::CHANNEL_GLOVO => 'Glovo',
            self::CHANNEL_YE => 'Yandex Eda',
            self::CHANNEL_JUSAN => 'Jusan',
            self::CHANNEL_FORTE => 'Forte Market',
            self::CHANNEL_BNPL => 'BNPL',
            self::CHANNEL_SITE_MARWIN => 'Сайт Marwin',
            self::CHANNEL_SITE_MELOMAN => 'Сайт Меломан',
            self::CHANNEL_OZON => 'Ozon Market',
            self::CHANNEL_WB => 'Wildberries',
            self::CHANNEL_APP_IOS => 'App iOS',
            self::CHANNEL_APP_ANDROID => 'App Android',
            self::CHANNEL_CERTIFICATE => 'Certificate',
        ];
    }

    /**
     * @param $channel
     * @return string
     * @throws Exception
     */
    public static function getChannel($channel): string
    {
        return ArrayHelper::getValue(static::getChannels(), $channel);
    }

    /**
     * @param Order $order
     * @return string|null
     */
    public static function getHandlerName(Order $order): ?string
    {
        return $order->handler ? UserHelper::getShortName($order->handler) : null;
    }

    /**
     * @param Order $order
     * @return string|null
     * @throws Exception
     */
    public static function getCreated(Order $order): ?string
    {
        return Yii::$app->formatter->asDatetime($order->created_at);
    }

    /**
     * @param Order $order
     * @return float
     */
    public static function getAmount(Order $order): float
    {
        return floor($order->amount);
    }

    /**
     * @param Order $order
     * @return float
     */
    public static function getAmountTotal(Order $order): float
    {
        return floor($order->amount + $order->delivery_cost);
    }

    /**
     * @param Order $order
     * @return float
     * @throws Exception
     */
    public static function getBonusAmount(Order $order): float
    {
        $paymentMethods = ArrayHelper::getValue($order->extra_fields, 'payment_methods', []);
        $result = 0;

        foreach ($paymentMethods as $method){
            if ($method['method'] == PaymentHelper::METHOD_BONUS){
                $result += (float)$method['amount'];
            }
        }

        return $result;
    }

    /**
     * @param Order $order
     * @return float
     * @throws Exception
     */
    public static function getBonusUsedAmount(Order $order): float
    {
        $amount = 0;

        if ($order->payment_method !== PaymentHelper::METHOD_MIXED) {
            return $amount;
        }

        foreach ($order->products as $orderProduct) {
            if (!$bonus = OrderProductHelper::getBonus($orderProduct)) {
                continue;
            }

            $amount += $bonus;
        }

        return $amount;
    }

    /**
     * @param Order $order
     * @return float
     */
    public static function getDeliveryCost(Order $order): float
    {
        return floor($order->delivery_cost);
    }

    /**
     * @param Order $order
     * @return string
     */
    public static function getDeliveryCostLabel(Order $order): string
    {
        if ($order->delivery_cost <= self::DELIVERY_COST_FREE) {
            return 'Бесплатно';
        }

        return Yii::$app->formatter->asDecimal(floor($order->delivery_cost)) . ' ₸';
    }

    /**
     * @param Order $order
     * @return mixed
     * @throws Exception
     */
    public static function getDeliveryCode(Order $order): mixed
    {
        return ArrayHelper::getValue($order->extra_fields, 'code');
    }

    /**
     * @param Order $order
     * @return mixed
     * @throws Exception
     */
    public static function getDeliveryQr(Order $order): mixed
    {
        return ArrayHelper::getValue($order->extra_fields, 'qr');
    }

    /**
     * @param Order $order
     * @return float
     * @throws Exception
     */
    public static function getAmountTotalWithBonus(Order $order): float
    {
        return floor($order->amount + $order->delivery_cost - self::getBonusUsedAmount($order));
    }

    /**
     * @param Order $order
     * @return string
     * @throws Exception
     */
    public static function getCostTotalLabel(Order $order): string
    {
        return Yii::$app->formatter->asDecimal(static::getAmountTotal($order)) . ' ₸';
    }

    /**
     * @param Order $order
     * @return string|null
     * @throws Exception
     */
    public static function getDeliveryLabel(Order $order): ?string
    {
        if (!$order->delivery_method) {
            return null;
        }

        $name = DeliveryHelper::getMethodName($order->delivery_method);

        if ($order->store) {
            return Yii::t('app', '{name} from store #{store}', ['name' => $name, 'store' => StoreHelper::getNameShort($order->store)]);
        }

        return $name;
    }

    /**
     * @param Order $order
     * @return string|null
     * @throws Exception
     */
    public static function getPaymentLabel(Order $order): ?string
    {
        if (!$order->payment_method) {
            return null;
        }

        if ($order->payment_method == PaymentHelper::METHOD_MIXED) {
            $result = [];
            $methods = self::getPaymentMethods($order);

            foreach ($methods as $method) {
                $result[] = PaymentHelper::getMethodName($method['method']);
            }

            return implode(' + ', $result);
        }

        return PaymentHelper::getMethodName($order->payment_method);
    }

    /**
     * @param Order $order
     * @return mixed
     * @throws Exception
     */
    public static function getPaymentMethods(Order $order): mixed
    {
        return ArrayHelper::getValue($order->extra_fields, 'payment_methods');
    }

    /**
     * @param Order $order
     * @return array|null[]|string[]
     * @throws Exception
     */
    public static function getAvailableStatuses(Order $order): array
    {
        $isAdmin = UserHelper::isAdmin();

        switch ($order->status) {
            case self::STATUS_NEW:
                return [
                    self::STATUS_NEW => self::getStatusName(self::STATUS_NEW),
                    self::STATUS_ACCEPTED => self::getStatusName(self::STATUS_ACCEPTED),
                ];
            case self::STATUS_ACCEPTED:
                return [
                    self::STATUS_ACCEPTED => self::getStatusName(self::STATUS_ACCEPTED),
                    self::STATUS_SHIPPED => self::getStatusName(self::STATUS_SHIPPED),
                    self::STATUS_PICKUP => self::getStatusName(self::STATUS_PICKUP)
                ];
            case self::STATUS_SHIPPED:
                if ($isAdmin){
                    return [
                        self::STATUS_SHIPPED => self::getStatusName(self::STATUS_SHIPPED),
                        self::STATUS_COURIER => self::getStatusName(self::STATUS_COURIER),
                        self::STATUS_DELIVERED => self::getStatusName(self::STATUS_DELIVERED),
                    ];
                }

                return [
                    self::STATUS_SHIPPED => self::getStatusName(self::STATUS_SHIPPED),
                    self::STATUS_COURIER => self::getStatusName(self::STATUS_COURIER),
                ];
            case self::STATUS_COURIER:
                return [
                    self::STATUS_COURIER => self::getStatusName(self::STATUS_COURIER),
                    self::STATUS_DELIVERED => self::getStatusName(self::STATUS_DELIVERED),
                ];
            case self::STATUS_DELIVERED:
                return [
                    self::STATUS_DELIVERED => self::getStatusName(self::STATUS_DELIVERED),
                ];
            case self::STATUS_PICKUP:
                return [
                    self::STATUS_PICKUP => self::getStatusName(self::STATUS_PICKUP),
                    self::STATUS_ISSUED => self::getStatusName(self::STATUS_ISSUED),
                ];
            case self::STATUS_ISSUED:
                return [
                    self::STATUS_ISSUED => self::getStatusName(self::STATUS_ISSUED),
                ];
            case self::STATUS_CANCELLED:
                return [
                    self::STATUS_CANCELLED => self::getStatusName(self::STATUS_CANCELLED),
                ];
        }
        return [];
    }

    /**
     * @param Order $order
     * @return string|null
     * @throws Exception
     */
    public static function getReferral(Order $order): ?string
    {
        return ArrayHelper::getValue($order->extra_fields, 'referral');
    }

    /**
     * @param $number
     * @return string
     */
    public static function getPayLink($number): string
    {
        $token = (new HashCrypto())->make($number);

        if (YII_ENV_DEV) {
            return 'http://127.0.0.1:6802/payment/' . $token;
        }

        return 'https://api.servicemarwin.com/payment/' . $token;
    }

    /**
     * @param $number
     * @return string
     */
    public static function getPaySuccessLink($number): string
    {
        $token = (new HashCrypto())->make($number);

        return 'https://api.servicemarwin.com/payment/success/' . $token;
    }

    /**
     * @param $number
     * @return string
     */
    public static function getPayFailureLink($number): string
    {
        $token = (new HashCrypto())->make($number);

        return 'https://api.servicemarwin.com/payment/failure/' . $token;
    }

    /**
     * @param Order $order
     * @return mixed
     * @throws Exception
     */
    public static function getInvoicePdf(Order $order): mixed
    {
        return ArrayHelper::getValue($order->extra_fields, 'invoice');
    }

    /**
     * @param Order $order
     * @return mixed
     * @throws Exception
     */
    public static function getInvoiceXlsx(Order $order): mixed
    {
        return ArrayHelper::getValue($order->extra_fields, 'invoice_xlsx');
    }

    /**
     * @param $status
     * @return bool
     */
    public static function isCompleted($status): bool
    {
        return in_array($status, [self::STATUS_CANCELLED, self::STATUS_DELIVERED, self::STATUS_ISSUED]);
    }

    /**
     * @param OrderProduct $product
     * @return mixed
     * @throws Exception
     */
    public static function getType(OrderProduct $product): mixed
    {
        return ArrayHelper::getValue($product->extra_fields, 'type');
    }

    /**
     * @param Order $order
     * @return array
     * @throws Exception
     */
    public static function getMapData(Order $order): array
    {
        $city = $order->city;
        $stores = [];

        foreach ($order->stores as $orderStore) {
            foreach ($orderStore->orderStoreProducts as $orderStoreProduct) {
                if ($orderStoreProduct->quantity > 0){
                    $stores[] = $orderStore->store;
                    break;
                }
            }
        }

        $result = [
            'city_lat' => $city->getLat() ?: null,
            'city_lng' => $city->getLng() ?: null,
            'customer' => null,
            'stores' => []
        ];

        // Set customer
        if ($order->lat && $order->lng){
            $result['customer'] = [
                'lat' => $order->lat,
                'lng' => $order->lng,
                'name' => $order->name,
                'phone' => $order->phone,
                'address' => $order->address,
                'balloon' => implode('<br />', [
                    '<strong>' . Yii::t('app', 'Customer ID') . '</strong>: ' . $order->name,
                    '<strong>' . Yii::t('app', 'Phone') . '</strong>: ' . $order->phone,
                    '<strong>' . Yii::t('app', 'Customer Address') . '</strong>: ' . $order->address,
                ])
            ];
        }

        // Set stores
        foreach ($stores as $store) {
            if (!$store->lat || !$store->lng){
                continue;
            }
            $result['stores'][] = [
                'lat' => $store->lat,
                'lng' => $store->lng,
                'name' => $store->name,
                'address' => $store->address,
                'balloon' => implode('<br />', [
                    '<strong>' . $store->getAttributeLabel('name') . '</strong>: ' . $store->name,
                    '<strong>' . $store->getAttributeLabel('address') . '</strong>: ' . $store->address,
                ])
            ];
        }

        return $result;
    }

    /**
     * @param Order $order
     * @return Lead|null
     * @throws Exception
     */
    public static function getLead(Order $order): ?Lead
    {
        if (!$id = $order->getLeadId()){
            return null;
        }

        return Lead::findOne($id);
    }

    /**
     * @param Order $order
     * @return bool
     */
    public static function canUpdate(Order $order): bool
    {
        return true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public static function canActivate(Order $order): bool
    {
        $user = UserHelper::getIdentity();
        $roles = [
            UserHelper::ROLE_ADMIN
        ];
        if (!in_array($user->role, $roles)){
            return false;
        }

        if (!in_array($order->status, [self::STATUS_DELIVERED, self::STATUS_ISSUED])){
            return false;
        }

        return true;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public static function canUpdateSingle(Order $order): bool
    {
        return false;
    }

    /**
     * @param $quantity
     * @return string
     */
    public static function getQuantityLabel($quantity = null): string
    {
        return (float)$quantity . ' шт';
    }

    /**
     * @param $quantity
     * @return float
     */
    public static function getQuantity($quantity = null): float
    {
        return (float)$quantity;
    }

    /**
     * @param Order $order
     * @return array
     */
    public static function getProducts(Order $order): array
    {
        $result = [];

        foreach ($order->products as $product) {
            if ($product->quantity <= 0){
                continue;
            }

            $result[] = [
                'sku' => $product->sku,
                'quantity' => (float)$product->quantity
            ];
        }

        return $result;
    }

    /**
     * @param $deliveryMethod
     * @return int|null
     */
    public static function generateDeliveryCode($deliveryMethod): ?int
    {
        if (in_array($deliveryMethod, [DeliveryHelper::DELIVERY_EXPRESS, DeliveryHelper::DELIVERY_STANDARD])) {
            return rand(100000, 999999);
        }
        return null;
    }

    /**
     * @param Order $order
     * @return string
     * @throws Exception
     */
    public static function getChannelCode(Order $order): string
    {
        $data = [
            self::CHANNEL_CRM => 'crm',
            self::CHANNEL_KASPI_SHOP => 'kaspi_shop',
            self::CHANNEL_HALYK_MARKET => 'halyk_market',
            self::CHANNEL_WOLT => 'wolt',
            self::CHANNEL_GLOVO => 'glovo',
            self::CHANNEL_JUSAN => 'jusan',
            self::CHANNEL_FORTE => 'forte',
            self::CHANNEL_BNPL => 'bnpl',
            self::CHANNEL_YE => 'ye',
            self::CHANNEL_SITE_MARWIN => 'site_marwin',
            self::CHANNEL_SITE_MELOMAN => 'site_meloman',
            self::CHANNEL_OZON => 'ozon',
            self::CHANNEL_WB => 'wb',
            self::CHANNEL_APP_IOS => 'ios',
            self::CHANNEL_APP_ANDROID => 'android',
        ];
        return ArrayHelper::getValue($data, $order->channel);
    }

    /**
     * @param Order $order
     * @return string
     * @throws Exception
     */
    public static function getPhoneWithExt(Order $order): string
    {
        $phone = PhoneHelper::getMaskPhone($order->phone);
        $ext = ArrayHelper::getValue($order->extra_fields, 'phone_ext');

        if ($ext) {
            return $phone . ' доб. ' . $ext;
        }

        return $phone;
    }

    /**
     * @param Order $order
     * @return string|null
     */
    public static function getExternalNumber(Order $order): ?string
    {
        if (!$order->vendor_number){
            return null;
        }

        return match ($order->channel) {
            self::CHANNEL_KASPI_SHOP => '701' . $order->vendor_number,
            self::CHANNEL_OZON, self::CHANNEL_SITE_MARWIN, self::CHANNEL_SITE_MELOMAN => $order->vendor_number,
            self::CHANNEL_WB => 'WB-' . $order->vendor_number,
            default => null,
        };
    }

    /**
     * @param Order $order
     * @return string|null
     * @throws Exception
     */
    public static function getWaybill(Order $order): ?string
    {
        return ArrayHelper::getValue($order->extra_fields, 'waybill');
    }

    /**
     * @param Order $order
     * @return mixed
     * @throws Exception
     */
    public static function getShipmentDate(Order $order): mixed
    {
        $today = date('Y-m-d');

        if ($order->channel == self::CHANNEL_OZON) {
            $shipmentDat = ArrayHelper::getValue($order->extra_fields, 'shipment_date');
            return $shipmentDat ? date('Y-m-d', $shipmentDat) : $today;
        }

        return ArrayHelper::getValue($order->extra_fields, 'shipment_date', $today);
    }

    /**
     * @return string[]
     */
    public static function getAccountArray(): array
    {
        return [
            self::ACCOUNT_MHV => 'MHV',
            self::ACCOUNT_PTW => 'PTW',
        ];
    }

    /**
     * @param Order $order
     * @return string|null
     * @throws Exception
     */
    public static function getAccountName(Order $order): ?string
    {
        if ($order->channel == self::CHANNEL_WB){
            return WbHelper::getAccountName($order->account_id);
        }
        if ($order->channel == self::CHANNEL_OZON){
            return OzonHelper::getAccountName($order->account_id);
        }
        if ($order->channel == self::CHANNEL_KASPI_SHOP){
            return ArrayHelper::getValue(self::getAccountArray(), $order->account_id);
        }

        return null;
    }

    /**
     * @param Order $order
     * @return string|null
     */
    public static function getVendorId(Order $order): ?string
    {
        if (self::isChannelMobile($order)) {
            return null;
        }
        return $order->vendor_id;
    }
}