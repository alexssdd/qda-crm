<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use yii\web\Controller;
use app\entities\Product;
use app\entities\Merchant;
use app\search\CartSearch;
use yii\helpers\ArrayHelper;
use app\services\CartService;
use yii\filters\AccessControl;
use app\forms\AddressSelectForm;
use app\core\helpers\CityHelper;
use app\core\helpers\UserHelper;
use app\services\ConsoleService;
use yii\web\NotFoundHttpException;
use app\forms\cart\CartCreateForm;
use app\forms\cart\CartStoresForm;
use app\core\helpers\MerchantHelper;
use app\forms\cart\CartCustomerForm;
use app\forms\cart\CartDefecturaForm;
use app\forms\cart\CartCalcProductsForm;
use app\forms\cart\CartCalcDeliveryForm;
use app\core\helpers\AddressSelectHelper;
use app\services\order\OrderNotifyService;
use app\modules\stock\services\StockAvailableService;

/**
 * Cart controller
 */
class CartController extends Controller
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
     * @return string|Response
     * @throws Exception
     */
    public function actionIndex()
    {
        $model = new CartCreateForm();
        $model->created_by = UserHelper::getIdentity()->id;
        $model->city_id = CityHelper::ID_ALMATY;

        if ($leadId = Yii::$app->request->get('lead_id')){
            $model->loadLead($leadId);
        }

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorMessage());
                }

                // Run service
                $order = (new CartService())->createOrder($model);

                // Run bot handle
                (new ConsoleService())->run('order/bot', [$order->id]);

                // Sms notify
                (new OrderNotifyService($order))->newOrder();

                Yii::$app->session->setFlash('success', Yii::t('app', 'Order successfully created'));
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(['/order/index']);
        }

        // Search products
        $searchModel = new CartSearch();
        $result = $searchModel->search([
            'merchant_id' => $model->merchant_id ?: ArrayHelper::getValue(array_keys(MerchantHelper::getSelectArray()), 0),
            'city_id' => $model->city_id ?: ArrayHelper::getValue(array_keys(CityHelper::getSelectArray()), 0),
        ]);

        return $this->renderAjax('index', [
            'model' => $model,
            'products' => $this->renderPartial('_search_result', [
                'result' => $result
            ])
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionSearch(): string
    {
        $searchModel = new CartSearch();
        $result = $searchModel->search(Yii::$app->request->queryParams);

        return $this->renderPartial('_search_result', [
            'result' => $result
        ]);
    }

    /**
     * @return Response
     */
    public function actionCustomer(): Response
    {
        $model = new CartCustomerForm();
        $model->load(Yii::$app->request->post());

        try {
            if (!$model->validate()){
                throw new DomainException($model->getErrorSummary(true)[0]);
            }

            // Run service
            $result = (new CartService())->customer($model);

            if (!$result){
                return $this->asJson([
                    'status' => 'not_found'
                ]);
            }

            return $this->asJson([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return $this->asJson([
                'status' => 'error',
                'data' => $e->getMessage()
            ]);
        }
    }

    /**
     * @return Response
     */
    public function actionCalcProducts(): Response
    {
        $model = new CartCalcProductsForm();
        $model->load(Yii::$app->request->post());

        try {
            if (!$model->validate()){
                throw new DomainException($model->getErrorSummary(true)[0]);
            }

            // Run service
            $result = (new CartService())->calcProducts($model);

            return $this->asJson([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return $this->asJson([
                'status' => 'error',
                'data' => $e->getMessage()
            ]);
        }
    }

    /**
     * @return Response
     */
    public function actionCalcDelivery(): Response
    {
        $model = new CartCalcDeliveryForm();
        $model->load(Yii::$app->request->post());

        try {
            if (!$model->validate()){
                throw new DomainException($model->getErrorMessage());
            }

            // Run service
            $result = (new CartService())->calcDelivery($model);

            return $this->asJson([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (Exception $e) {
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param $merchantId
     * @param $cityId
     * @param $productId
     * @return string|Response
     */
    public function actionStockOnline($merchantId, $cityId, $productId)
    {
        try {
            if (!$merchant = Merchant::findOne($merchantId)) {
                throw new DomainException("Merchant id: $merchantId not found");
            }
            $product = $this->findProduct($productId);
            $products = [
                [
                    'sku' => $product->sku,
                    'quantity' => 1
                ]
            ];
            $service = new StockAvailableService($merchant, $cityId, $products);
            $stores = $service->getProductStores();

            return $this->renderAjax('_stock_online', [
                'product' => $product,
                'stores' => $stores
            ]);
        } catch (DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Internal server error');
        }

        return $this->redirect(['order/index']);
    }

    /**
     * @param $merchantId
     * @param $productId
     * @return string|Response
     */
    public function actionStockCity($merchantId, $productId)
    {
        try {
            if (!$merchant = Merchant::findOne($merchantId)) {
                throw new DomainException("Merchant id: $merchantId not found");
            }
            $product = $this->findProduct($productId);
            $products = [
                [
                    'sku' => $product->sku,
                    'quantity' => 1
                ]
            ];
            $service = new StockAvailableService($merchant, null, $products);
            $cities = $service->getProductCities();

            return $this->renderAjax('_stock_city', [
                'product' => $product,
                'cities' => $cities
            ]);
        } catch (DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Internal server error');
        }

        return $this->redirect(['order/index']);
    }

    /**
     * @param $productId
     * @param $cityId
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionDefectura($productId, $cityId = null)
    {
        $product = $this->findProduct($productId);
        $model = new CartDefecturaForm();
        $model->product_name = $product->name;
        $model->product_id = $productId;
        $model->city_id = $cityId;
        $model->quantity = 1;

        try {
            if ($model->load(Yii::$app->request->post())){
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new CartService())->defectura($model);

                return $this->asJson([
                    'status' => 'success'
                ]);
            }
        } catch (Exception $e) {
            return $this->asJson([
                'status' => 'error',
                'data' => $e->getMessage()
            ]);
        }

        return $this->renderAjax('_defectura', [
            'model' => $model
        ]);
    }

    /**
     * @return string
     */
    public function actionStores(): string
    {
        $model = new CartStoresForm();
        $model->load(Yii::$app->request->post());

        try {
            if (!$model->validate()){
                throw new DomainException($model->getErrorSummary(true)[0]);
            }

            $stores = (new CartService())->stores($model);

            return $this->renderAjax('_stores', [
                'stores' => $stores,
                'error' => null,
            ]);
        } catch (Exception $e){
            return $this->renderAjax('_stores', [
                'stores' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * @return string
     */
    public function actionAddressSelect(): string
    {
        $attributes = Yii::$app->request->post();

        return $this->renderAjax('_address_select', [
            'attributes' => $attributes
        ]);
    }

    /**
     * @return Response
     */
    public function actionAddressSave(): Response
    {
        $model = new AddressSelectForm();

        if (!$model->load(Yii::$app->request->post())){
            return $this->asJson([
                'status' => 'error',
                'message' => 'Empty post'
            ]);
        }

        $label = AddressSelectHelper::getLabel($model->address, $model->type, $model->title);
        return $this->asJson([
            'status' => 'success',
            'data' => [
                'label' => $label,
                'address' => $model->address,
                'type' => $model->type,
                'lat' => $model->lat,
                'lng' => $model->lng,
                'title' => $model->title,
                'house' => $model->house,
                'apartment' => $model->apartment,
                'intercom' => $model->intercom,
                'entrance' => $model->entrance,
                'floor' => $model->floor
            ]
        ]);
    }

    /**
     * @param $id
     * @return Product
     * @throws NotFoundHttpException
     */
    protected function findProduct($id): Product
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}