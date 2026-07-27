<?php

namespace app\services;

use Exception;
use DomainException;
use app\modules\auth\models\User;
use app\modules\order\models\Order;
use yii\helpers\ArrayHelper;
use app\core\helpers\UserHelper;
use app\core\helpers\OrderHelper;

/**
 * Operator service
 */
class OperatorService
{
    /**
     * @return User
     */
    public function getOperator(): User
    {
        $users = $this->getOperators();

        if (!$users){
            throw new DomainException('Не удалось получить свободных операторов');
        }

        // Sort by order
        ArrayHelper::multisort($users, 'orders_count');

        return $users[0];
    }

    /**
     * @return User[]
     */
    public function getOperators(): array
    {
        $orderStatuses = implode(',', [OrderHelper::STATUS_NEW, OrderHelper::STATUS_ACCEPTED]);

        return User::find()
            ->alias('users')
            ->select([
                'users.*',
                'COUNT(orders.id) AS orders_count'
            ])
            ->andWhere(['users.role' => UserHelper::ROLE_OPERATOR])
            ->andWhere(['users.state' => UserHelper::STATE_ONLINE])
            ->andWhere(['users.status' => UserHelper::STATUS_ACTIVE])
            ->leftJoin(['orders' => Order::tableName()], implode(' AND ', [
                'users.id = orders.handler_id',
                'orders.status IN (' . $orderStatuses . ')'
            ]))
            ->groupBy(['users.id'])
            ->all();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getAll(): array
    {
        /** @var User[] $users */
        $users = User::find()
            ->andWhere(['role' => UserHelper::ROLE_OPERATOR])
            ->andWhere(['status' => UserHelper::STATUS_ACTIVE])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $result = [];
        foreach ($users as $user) {
            $result[$user->id] = $user->name;
        }

        return $result;
    }
}
