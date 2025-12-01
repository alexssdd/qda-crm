<?php

namespace app\repositories;

use DomainException;
use app\entities\OrderProduct;

/**
 * Order product repository
 */
class OrderProductRepository
{
    /**
     * @param $id
     * @return OrderProduct
     */
    public static function getById($id): OrderProduct
    {
        return (new OrderProductRepository)->getBy(['id' => $id]);
    }

    /**
     * @param array $condition
     * @return OrderProduct
     */
    protected function getBy(array $condition): OrderProduct
    {
        if (!$model = OrderProduct::find()->andWhere($condition)->limit(1)->one()) {
            throw new DomainException('The order product not found.');
        }

        return $model;
    }
}