<?php

namespace app\controllers;

use Yii;
use Exception;
use yii\web\Response;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\core\helpers\UserHelper;
use app\search\chart\SaleSearch;
use app\search\chart\ChatSearch;
use app\search\chart\OrderSearch;
use app\search\chart\ProductSearch;
use app\search\chart\DeliverySearch;
use app\search\chart\OperatorSearch;

/**
 * Class ChartController
 * @package app\controllers
 */
class ChartController extends Controller
{
    public $layout = 'chart';

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
                    ]
                ],
            ],

        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    /**
     * @return string
     */
    public function actionOperator(): string
    {
        return $this->render('operator');
    }

    /**
     * @return string
     */
    public function actionDashboard(): string
    {
        return $this->render('dashboard');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionSaleChannels(): string
    {
        $searchModel = new SaleSearch([
            'date_from' => date('Y-m-01'),
            'date_to' => date('Y-m-d', strtotime('last day of this month')),
        ]);
        $data = $searchModel->channels(Yii::$app->request->queryParams);

        return $this->renderPartial('sale/channels_data', [
            'data' => $data
        ]);
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function actionSaleStatus(): Response
    {
        $searchModel = new SaleSearch([
            'date_from' => date('Y-m-d', strtotime('-6 days')),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->status(Yii::$app->request->queryParams);

        return $this->asJson($data);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionSaleOperator(): string
    {
        $searchModel = new SaleSearch([
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->operator(Yii::$app->request->queryParams);

        return $this->renderPartial('sale/operator_data', [
            'data' => $data
        ]);
    }

    /**
     * @return Response
     */
    public function actionSaleMonth(): Response
    {
        $searchModel = new SaleSearch([
            'date_from' => date('Y-m-01', strtotime('-11 month')),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->month(Yii::$app->request->queryParams);

        return $this->asJson($data);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionDeliveryAverage(): string
    {
        $searchModel = new DeliverySearch([
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->average(Yii::$app->request->queryParams);

        return $this->renderPartial('delivery/average_data', [
            'data' => $data
        ]);
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function actionOrderHandler(): Response
    {
        $searchModel = new OrderSearch([
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->handler(Yii::$app->request->queryParams);

        return $this->asJson($data);
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function actionOrderCompleted(): Response
    {
        $searchModel = new OrderSearch([
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->completed(Yii::$app->request->queryParams);

        return $this->asJson($data);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionOrderAverageHandle(): string
    {
        $searchModel = new OrderSearch([
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->averageHandle(Yii::$app->request->queryParams);

        return $this->renderPartial('order/average_handle_data', [
            'data' => $data
        ]);
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function actionProductCategory(): Response
    {
        $searchModel = new ProductSearch([
            'date_from' => date('Y-m-01'),
            'date_to' => date('Y-m-d', strtotime('last day of this month')),
        ]);
        $data = $searchModel->category(Yii::$app->request->queryParams);

        return $this->asJson($data);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionChatCount(): string
    {
        $searchModel = new ChatSearch([
            'date_from' => date('Y-m-d'),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->count(Yii::$app->request->queryParams);

        return $this->renderPartial('chat/count_data', [
            'data' => $data
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionLongHandle(): string
    {
        $searchModel = new OperatorSearch([
            'date_from' => date('Y-m-d', strtotime('-2 days')),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->longHandle(Yii::$app->request->queryParams);

        return $this->renderPartial('operator/long_handle_data', [
            'data' => $data
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionWithoutAttention(): string
    {
        $searchModel = new OperatorSearch([
            'date_from' => date('Y-m-d', strtotime('-2 days')),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->withoutAttention(Yii::$app->request->queryParams);

        return $this->renderPartial('operator/without_attention_data', [
            'data' => $data
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionExpressSearch(): string
    {
        $searchModel = new OperatorSearch([
            'date_from' => date('Y-m-d', strtotime('-2 days')),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->expressSearch(Yii::$app->request->queryParams);

        return $this->renderPartial('operator/express_search_data', [
            'data' => $data
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionExpressLong(): string
    {
        $searchModel = new OperatorSearch([
            'date_from' => date('Y-m-d', strtotime('-2 days')),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->expressLong(Yii::$app->request->queryParams);

        return $this->renderPartial('operator/express_long_data', [
            'data' => $data
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionStandardLong(): string
    {
        $searchModel = new OperatorSearch([
            'date_from' => date('Y-m-d', strtotime('-2 days')),
            'date_to' => date('Y-m-d'),
        ]);
        $data = $searchModel->standardLong(Yii::$app->request->queryParams);

        return $this->renderPartial('operator/standard_long_data', [
            'data' => $data
        ]);
    }

    /**
     * @return string
     * todo
     */
    public function actionSalePlan(): string
    {
        return $this->renderPartial('sale/plan_data');
    }

    /**
     * @return string
     * todo
     */
    public function actionOrderDelivery(): string
    {
        return $this->renderPartial('order/delivery_data');
    }

    /**
     * @return string
     * todo
     */
    public function actionOrderCancel(): string
    {
        return $this->renderPartial('order/cancel_data');
    }

}