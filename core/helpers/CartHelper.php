<?php

namespace app\core\helpers;

/**
 * Cart helper
 */
class CartHelper
{
    /**
     * @return array|array[]
     */
    public static function getParams(): array
    {
        $result = [
            'merchants' => [],
            'delivery_methods' => [],
            'payment_methods' => []
        ];
        $merchants = MerchantHelper::getArrayByCode();
        $deliveryMethods = DeliveryHelper::getMethods();
        $paymentMethods = PaymentHelper::getCartMethods();

        // Merchants
        foreach ($merchants as $code => $merchant) {
            if ($code == MerchantHelper::CODE_MARWIN){
                $result['merchants'][] = [
                    'id' => $merchant->id,
                    'value' => $merchant->name
                ];
            }
        }

        // Delivery methods
        foreach ($deliveryMethods as $method => $methodName) {
            $result['delivery_methods'][] = [
                'id' => $method,
                'value' => $methodName
            ];
        }

        // Payment methods
        foreach ($paymentMethods as $method => $methodName) {
            $result['payment_methods'][] = [
                'id' => $method,
                'value' => $methodName
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public static function getDeliveryParams(): array
    {
        $paymentMethods = PaymentHelper::getCartMethods();
        $paymentMethodsDelivery = $paymentMethods;
        unset($paymentMethodsDelivery[PaymentHelper::PAYMENT_CASH]);

        return [
            DeliveryHelper::DELIVERY_PICKUP => [
                'show_address' => false,
                'show_store' => true,
                'required_store' => true,
                'payment_methods' => $paymentMethods
            ],
            DeliveryHelper::DELIVERY_STANDARD => [
                'show_address' => true,
                'show_store' => false,
                'required_store' => false,
                'payment_methods' => $paymentMethodsDelivery
            ],
            DeliveryHelper::DELIVERY_EXPRESS => [
                'show_address' => true,
                'show_store' => false,
                'required_store' => false,
                'payment_methods' => $paymentMethodsDelivery
            ],
            DeliveryHelper::DELIVERY_EMEX => [
                'show_address' => true,
                'show_store' => false,
                'required_store' => false,
                'payment_methods' => $paymentMethodsDelivery
            ]
        ];
    }
}