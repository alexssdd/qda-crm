<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use app\entities\Care;
use app\entities\Order;
use yii\web\Controller;
use app\entities\Customer;
use yii\filters\AccessControl;
use app\services\AppealService;
use app\core\helpers\UserHelper;
use app\core\helpers\CityHelper;
use yii\web\NotFoundHttpException;
use app\forms\appeal\AppealCreateForm;
use app\forms\appeal\AppealCustomerForm;

/**
 * Appeal controller
 */
class AppealController extends Controller
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
        $model = new AppealCreateForm();
        $model->city_id = CityHelper::ID_ALMATY;
        $model->created_by = UserHelper::getIdentity()->id;

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new AppealService())->createCare($model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Care successfully created'));
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(['/care/index']);
        }

        return $this->renderAjax('index', [
            'model' => $model
        ]);
    }

    /**
     * @return Response
     */
    public function actionCustomer(): Response
    {
        $model = new AppealCustomerForm();
        $model->load(Yii::$app->request->post());

        try {
            if (!$model->validate()){
                throw new DomainException($model->getErrorSummary(true)[0]);
            }

            // Run service
            $result = (new AppealService())->customer($model);

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
     * @param $customerId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCustomerCares($customerId): string
    {
        $customer = $this->findCustomer($customerId);

        // Get data
        $data = Care::find()
            ->andWhere(['customer_id' => $customer->id])
            ->limit(30)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->renderAjax('_customer_cares', [
            'customer' => $customer,
            'data' => $data
        ]);
    }

    /**
     * @param $customerId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCustomerOrders($customerId): string
    {
        $customer = $this->findCustomer($customerId);

        // Get data
        $data = Order::find()
            ->andWhere(['customer_id' => $customer->id])
            ->limit(30)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->renderAjax('_customer_orders', [
            'customer' => $customer,
            'data' => $data
        ]);
    }

    /**
     * @param $id
     * @return Customer
     * @throws NotFoundHttpException
     */
    protected function findCustomer($id): Customer
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
