<?php

namespace app\controllers;

use Yii;
use Throwable;
use Exception;
use DomainException;
use yii\web\Response;
use app\entities\Store;
use yii\web\Controller;
use app\entities\Order;
use app\search\CartSearch;
use app\search\OrderSearch;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use app\entities\OrderReceipt;
use app\forms\AddressSelectForm;
use app\core\helpers\TextHelper;
use app\services\ConsoleService;
use app\core\helpers\UserHelper;
use app\core\helpers\StoreHelper;
use app\core\helpers\OrderHelper;
use app\services\OperatorService;
use yii\web\NotFoundHttpException;
use app\forms\order\OrderUpdateForm;
use app\forms\order\OrderCancelForm;
use app\forms\order\OrderPendingForm;
use app\core\helpers\OrderEventHelper;
use app\forms\order\OrderTransferForm;
use app\forms\order\OrderAssemblyForm;
use app\forms\order\OrderWhatsappForm;
use app\forms\order\OrderProductAddForm;
use app\services\order\OrderBonusService;
use app\forms\order\OrderAssemblyAllForm;
use app\forms\order\OrderChatMessageForm;
use app\services\order\OrderEventService;
use app\services\order\OrderRulesService;
use app\modules\kaspi\helpers\KaspiHelper;
use app\services\order\OrderAssignService;
use app\services\order\OrderManageService;
use app\services\order\OrderReasonService;
use app\forms\order\OrderProductUpdateForm;
use app\services\order\OrderProductService;
use app\services\order\OrderAddressService;
use app\services\order\OrderHistoryService;
use app\services\order\OrderWhatsappService;
use app\services\order\OrderAssemblyService;
use app\repositories\OrderProductRepository;
use app\modules\kaspi\services\KaspiOrderService;

/**
 * Order controller
 */
class OrderController extends Controller
{
    /**
     * @return array|array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserHelper::ROLE_OPERATOR],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex(): string
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $order = null;
        if ($searchModel->id){
            $order = $this->getOrder($searchModel->id);
        }

        return $this->render('index', [
            'order' => $order,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id): Response
    {
        $order = $this->getOrder($id);
        $prevStatus = $order->status;
        $form = new OrderUpdateForm($order);
        $form->load(Yii::$app->request->post(), '');

        try {
            if (!$form->validate()) {
                throw new DomainException($form->getErrorMessage());
            }

            (new OrderRulesService($order))->hasAssembly($prevStatus, $form->status);
            (new OrderManageService($order))->update($form);
            (new OrderHistoryService($order, UserHelper::getIdentity()))->create($prevStatus, $order->status);

            (new ConsoleService())->run('order/sync', [$order->id, $prevStatus, UserHelper::getIdentity()->getId()]);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(Yii::$app->request->referrer ?: 'order/index');
    }

    /**
     * @param $orderProductId
     * @return string|Response
     * @throws Throwable
     */
    public function actionAddAssemblyStock($orderProductId)
    {
        $orderProduct = OrderProductRepository::getById($orderProductId);
        $form = new OrderAssemblyForm($orderProduct);
        $user = UserHelper::getIdentity();
        $order = $orderProduct->order;

        if ($form->load(Yii::$app->request->post(), '')) {
            try {
                (new OrderRulesService($order))->notAllowedAssembly();

                if (!$form->validate()) {
                    throw new DomainException($form->getErrorMessage());
                }

                (new OrderAssemblyService($order, $user))->assemblyOrderProduct($orderProduct, $form);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->referrer ?: 'order/index');
        }

        $service = new OrderAssemblyService($order, $user);

        return $this->renderAjax('_add_assembly_stock', [
            'orderProduct' => $orderProduct,
            'stores' => $service->getProductStores($orderProduct)
        ]);
    }

    /**
     * @param $orderProductId
     * @return string|Response
     * @throws Throwable
     */
    public function actionAddAssemblyManual($orderProductId)
    {
        $orderProduct = OrderProductRepository::getById($orderProductId);
        $form = new OrderAssemblyForm($orderProduct);
        $user = UserHelper::getIdentity();
        $order = $orderProduct->order;

        if ($form->load(Yii::$app->request->post(), '')) {
            try {
                (new OrderRulesService($order))->notAllowedAssembly();

                if (!$form->validate()) {
                    throw new DomainException($form->getErrorMessage());
                }

                (new OrderAssemblyService($order, $user))->assemblyOrderProduct($orderProduct, $form);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->referrer ?: 'order/index');
        }

        $service = new OrderAssemblyService($order, $user);

        return $this->renderAjax('_add_assembly_manual', [
            'orderProduct' => $orderProduct,
            'stores' => $service->getAssemblyManual($orderProduct)
        ]);
    }

    /**
     * @param $orderProductId
     * @return string|Response
     * @throws Throwable
     */
    public function actionAddAssemblyAll($orderProductId)
    {
        $orderProduct = OrderProductRepository::getById($orderProductId);
        $form = new OrderAssemblyAllForm();
        $user = UserHelper::getIdentity();
        $order = $orderProduct->order;

        if ($form->load(Yii::$app->request->post(), '')) {
            try {
                (new OrderRulesService($order))->notAllowedAssembly();

                if (!$form->validate()) {
                    throw new DomainException($form->getErrorMessage());
                }

                $store = Store::findOne($form->store_id);
                if (!$store){
                    throw new NotFoundHttpException('The store was not found');
                }

                // Assembly
                (new OrderAssemblyService($order, $user))->assemblyStore($store);

                // Create event
                $eventService = new OrderEventService($order, $user);
                $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $store->id, 'name' => StoreHelper::getNameShort($store)]);

                // Continue after assembly
                (new ConsoleService())->run('order/assembly-continue', [$order->id, $store->id]);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->referrer ?: 'order/index');
        }

        $service = new OrderAssemblyService($order, $user);

        return $this->renderAjax('_add_assembly_all', [
            'stores' => $service->getProductStores($orderProduct),
            'orderProduct' => $orderProduct
        ]);
    }

    /**
     * @param $orderProductId
     * @return Response
     * @throws Throwable
     */
    public function actionRemoveAssembly($orderProductId): Response
    {
        $user = UserHelper::getIdentity();

        // Transaction
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $orderProduct = OrderProductRepository::getById($orderProductId);
            $order = $orderProduct->order;

            (new OrderRulesService($order))->hasReceipt();

            // Remove order product
            (new OrderAssemblyService($order, $user))->removeAssembly($orderProduct);

            // Transaction
            $transaction->commit();

            return $this->asJson([
                'status' => 'success',
                'assemblies' => ''
            ]);
        } catch (Exception $e){
            // Transaction
            $transaction->rollBack();

            return $this->asJson([
                'status' => 'error',
                'assemblies' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param $id
     * @return Response
     * @throws Throwable
     */
    public function actionRemoveAssemblyAll($id): Response
    {
        $user = UserHelper::getIdentity();

        // Transaction
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $order = $this->getOrder($id);

            (new OrderRulesService($order))->hasReceipt();

            // Remove order product
            (new OrderAssemblyService($order, $user))->removeAssemblyAll();

            // Transaction
            $transaction->commit();

            return $this->asJson([
                'status' => 'success',
                'assemblies' => ''
            ]);
        } catch (Exception $e){
            // Transaction
            $transaction->rollBack();

            return $this->asJson([
                'status' => 'error',
                'assemblies' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionAddProduct($id)
    {
        $order = $this->getOrder($id);
        $prevStatus = $order->status;
        $form = new OrderProductAddForm($order);
        $user = UserHelper::getIdentity();

        if ($form->load(Yii::$app->request->post(), '')) {
            try {
                if (!$form->validate()) {
                    throw new DomainException($form->getErrorMessage());
                }

                (new OrderProductService($order))->add($form);
                (new ConsoleService())->run('order/sync', [$order->id, $prevStatus, $user->getId()]);
                (new OrderBonusService($order, $user))->distribute();

                Yii::$app->session->setFlash('success', Yii::t('app', 'Product successfully added'));
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->referrer ?: 'order/index');
        }

        // Search products
        $searchModel = new CartSearch();
        $result = $searchModel->search([
            'merchant_id' => $order->merchant_id,
            'city_id' => $order->city_id,
        ]);

        return $this->renderAjax('_add_product', [
            'order' => $order,
            'products' => $this->renderPartial('_search_result', [
                'result' => $result
            ])
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdateProducts($id)
    {
        $order = $this->getOrder($id);
        $prevStatus = $order->status;
        $prevProducts = KaspiHelper::getProducts($order);
        $form = new OrderProductUpdateForm($order);
        $user = UserHelper::getIdentity();

        if ($form->load(Yii::$app->request->post(), '')) {
            try {
                if (!$form->validate()) {
                    throw new DomainException($form->getErrorMessage());
                }

                (new OrderProductService($order))->update($form);
                (new KaspiOrderService($order))->handleUpdateProducts($prevProducts);
                (new ConsoleService())->run('order/sync', [$order->id, $prevStatus, $user->getId()]);
                (new OrderBonusService($order, $user))->distribute();

                Yii::$app->session->setFlash('success', Yii::t('app', 'Changes successfully saved'));
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->referrer ?: 'order/index');
        }
        return $this->renderAjax('_update_products', [
            'order' => $order
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionProductSearch($id): string
    {
        $order = $this->getOrder($id);
        $searchModel = new CartSearch([
            'merchant_id' => $order->merchant_id,
            'city_id' => $order->city_id,
            'customer_id' => $order->customer_id,
        ]);
        $result = $searchModel->search(Yii::$app->request->queryParams);

        return $this->renderPartial('_search_result', [
            'result' => $result
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionCancel($id)
    {
        $order = $this->getOrder($id);
        $user = UserHelper::getIdentity();
        $prevStatus = $order->status;
        $form = new OrderCancelForm();

        if ($form->load(Yii::$app->request->post())) {
            try {
                if (!$form->validate()) {
                    throw new DomainException($form->getErrorMessage());
                }

                $reason = (new OrderReasonService($order, $user))->cancel($form);
                (new OrderManageService($order))->cancelled();
                (new OrderHistoryService($order, $user))->create($prevStatus, $order->status);
                (new OrderEventService($order, $user))->create('', OrderEventHelper::TYPE_CANCEL, [
                    'reason' => $reason->reason,
                    'reason_additional' => $reason->reason_additional,
                ]);
                (new OrderAssemblyService($order, $user))->removeAssemblyAll();
                (new ConsoleService())->run('order/sync', [$order->id, $prevStatus, UserHelper::getIdentity()->getId()]);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->referrer ?: 'order/index');
        }

        return $this->renderAjax('_cancel', [
            'model' => $form
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionPending($id)
    {
        $order = $this->getOrder($id);
        $user = UserHelper::getIdentity();
        $form = new OrderPendingForm();

        if ($form->load(Yii::$app->request->post())) {
            try {
                if (!$form->validate()) {
                    throw new DomainException($form->getErrorMessage());
                }

                (new OrderManageService($order))->setPending();
                (new OrderEventService($order, $user))->create('', OrderEventHelper::TYPE_PENDING, ['reason' => $form->reason]);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->referrer ?: 'order/index');
        }

        return $this->renderAjax('_pending', [
            'model' => $form
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDelivery($id): string
    {
        $order = $this->getOrder($id);

        return $this->renderAjax('_delivery', [
            'order' => $order
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCourier($id): string
    {
        $order = $this->getOrder($id);

        return $this->renderAjax('_courier', [
            'order' => $order
        ]);
    }

    /**
     * @param $id
     * @return Response
     */
    public function actionCourierLocation($id): Response
    {
        try {
            $order = $this->getOrder($id);

            if (!$courier = $order->courier){
                throw new DomainException('Empty order courier');
            }

            return $this->asJson([
                'status' => 'success',
                'data' => [
                    'lat' => '43.24140705466217',
                    'lng' => '76.87786038574192',
                    'name' => $courier->name,
                    'phone' => $courier->phone,
                    'balloon' => implode('<br />', [
                        '<strong>' . $courier->getAttributeLabel('name') . '</strong>: ' . $courier->name,
                        '<strong>' . $courier->getAttributeLabel('phone') . '</strong>: ' . $courier->phone,
                    ])
                ]
            ]);
        } catch (Exception $e){
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionTransfer($id)
    {
        $order = $this->getOrder($id);
        $user = UserHelper::getIdentity();
        $model = new OrderTransferForm();

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()) {
                    throw new DomainException($model->getErrorMessage());
                }

                $executor = $model->getExecutor();
                if (!$executor){
                    throw new DomainException('The executor was not found');
                }

                // Transfer order
                (new OrderManageService($order))->transfer($model);

                // Assign order
                (new OrderAssignService($order, $executor))->assign();

                // Create message
                $message = TextHelper::transferOrder($executor->full_name);
                (new OrderEventService($order, $user))->create($message, OrderEventHelper::TYPE_TRANSFER);

                // Flash
                Yii::$app->session->setFlash('success', 'Заказ успешно передан');
            } catch (Exception $e) {
                // Flash
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->referrer ?: 'order/index');
        }

        $users = (new OperatorService())->getAll();

        return $this->renderAjax('_transfer', [
            'model' => $model,
            'users' => $users
        ]);
    }

    /**
     * @param $id
     * @return Response
     */
    public function actionChatMessage($id): Response
    {
        try {
            $order = $this->getOrder($id);
            $user = UserHelper::getIdentity();
            $model = new OrderChatMessageForm();
            $model->load(Yii::$app->request->post());

            if (!$model->validate()) {
                throw new DomainException($model->getErrorMessage());
            }

            // Create message
            (new OrderEventService($order, $user))->create($model->message, OrderEventHelper::TYPE_MESSAGE);

            return $this->asJson([
                'status' => 'success',
                'data' => $this->renderPartial('detail/chat', [
                    'order' => $order
                ])
            ]);
        } catch (Exception $e) {
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param $id
     * @return Response
     */
    public function actionWhatsapp($id): Response
    {
        try {
            $order = $this->getOrder($id);
            $model = new OrderWhatsappForm();
            $model->load(Yii::$app->request->post(), '');

            if (!$model->validate()) {
                throw new DomainException($model->getErrorMessage());
            }

            // Send message
            (new OrderWhatsappService($order, UserHelper::getIdentity()))->send($model);

            return $this->asJson([
                'status' => 'success',
                'data' => $this->renderPartial('detail/chat', [
                    'order' => $order
                ])
            ]);
        } catch (Exception $e) {
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAddress($id): string
    {
        $order = $this->getOrder($id);

        return $this->renderAjax('_address', [
            'order' => $order
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionAddressSave($id): Response
    {
        $order = $this->getOrder($id);
        $model = new AddressSelectForm($order);

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()) {
                    throw new DomainException($model->getErrorMessage());
                }

                // Change address
                (new OrderAddressService($order))->change($model);

                // Flash
                Yii::$app->session->setFlash('success', 'Адрес успешно изменен');
            } catch (Exception $e) {
                // Flash
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->redirect(Yii::$app->request->referrer ?: 'order/index');
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionReceipt($id): string
    {
        $receipt = $this->getReceipt($id);
        $order = $receipt->order;
        $orderProducts = ArrayHelper::index($order->products, 'sku');

        return $this->renderAjax('_receipt', [
            'order' => $order,
            'receipt' => $receipt,
            'orderProducts' => $orderProducts
        ]);
    }

    /**
     * @param $id
     * @return Order|null
     * @throws NotFoundHttpException
     */
    protected function getOrder($id): ?Order
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested order with {id} does not exist.', ['id' => $id]));
    }

    /**
     * @param $id
     * @return OrderReceipt|null
     * @throws NotFoundHttpException
     */
    protected function getReceipt($id): ?OrderReceipt
    {
        if (($model = OrderReceipt::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested order receipt with {id} does not exist.', ['id' => $id]));
    }
}
