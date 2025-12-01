<?php

namespace app\services\order;

use Exception;
use app\entities\User;
use app\entities\Order;
use app\core\helpers\OrderHelper;
use app\core\helpers\OrderEventHelper;
use yii\helpers\ArrayHelper;

/**
 * Order bonus service
 */
class OrderBonusService
{
    const MAX_PERCENT = 99;

    private $_order;
    private $_user;

    /**
     * @param Order $order
     * @param User $user
     */
    public function __construct(Order $order, User $user)
    {
        $this->_order = $order;
        $this->_user = $user;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function distribute(): void
    {
        $order = $this->_order;

        // Check channel
        $allowedChannels = [OrderHelper::CHANNEL_SITE_MARWIN, OrderHelper::CHANNEL_SITE_MELOMAN];
        if (!in_array($order->channel, $allowedChannels)){
            return;
        }

        // Check bonus
        $bonusTotal = OrderHelper::getBonusAmount($order);
        if (!$bonusTotal){
            return;
        }
        $bonusMax = round($order->amount * (self::MAX_PERCENT / 100));
        $bonusTotal = min($bonusMax, $bonusTotal);

        $orderProducts = $order->products;
        $products = [];
        $total = 0;
        foreach ($orderProducts as $i => $orderProduct){
            // Clear
            $extraFields = $orderProduct->extra_fields;
            ArrayHelper::remove($extraFields, 'bonus');
            ArrayHelper::remove($extraFields, 'bonus_percent');
            $orderProduct->extra_fields = $extraFields;
            $orderProduct->save();

            // Check quantity
            if ($orderProduct->quantity <= 0){
                continue;
            }

            // Check price
            if (!$orderProduct->price){
                continue;
            }

            $cost = $orderProduct->quantity * $orderProduct->price;
            $products[$i] = [
                'cost' => $cost,
                'percent' => null,
                'bonus' => null
            ];
            $total += $cost;
        }

        if (!$products){
            return;
        }

        $counterBonus = $bonusTotal;
        $counterPercent = 1;
        $countProducts = count($products);

        $calcResult = [];
        foreach ($products as $i => $product){
            // Prepare percent
            $percent = round(($product['cost'] / $total), 2);

            // Prepare bonus
            $bonusCalc = min($product['cost'], round($bonusTotal * $percent));

            // Last item
            if ($i == ($countProducts - 1)){
                $percent = $counterPercent;
                $bonusCalc = $counterBonus;
            }

            $orderProduct = $orderProducts[$i];
            $extraFields = $orderProduct->extra_fields;
            $extraFields['bonus'] = $bonusCalc;
            $extraFields['bonus_percent'] = $percent;
            $orderProduct->extra_fields = $extraFields;
            $orderProduct->save();

            // Update counter
            $counterBonus -= $bonusCalc;
            $counterPercent -= $percent;

            // Calc result
            $calcResult[] = [
                'sku' => $orderProduct->sku,
                'bonus' => $bonusCalc,
                'percent' => $percent
            ];
        }

        // Event
        (new OrderEventService($order, $this->_user))->create('', OrderEventHelper::TYPE_BONUS_DISTRIBUTE, ['calc' => $calcResult]);
    }
}