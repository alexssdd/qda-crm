<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use yii\web\Controller;
use app\entities\Address;
use app\entities\Customer;
use app\search\AddressSearch;
use yii\filters\AccessControl;
use app\forms\AddressCreateForm;
use app\forms\AddressUpdateForm;
use app\services\AddressService;
use app\core\helpers\UserHelper;
use yii\web\NotFoundHttpException;

/**
 * Address controller
 */
class AddressController extends Controller
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
                        'roles' => [UserHelper::ROLE_ADMINISTRATOR],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $customer_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($customer_id): string
    {
        $customer = $this->findCustomer($customer_id);
        $searchModel = new AddressSearch();
        $searchModel->customer_id = $customer_id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'customer' => $customer,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $customer_id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionCreate($customer_id)
    {
        $customer = $this->findCustomer($customer_id);
        $model = new AddressCreateForm();
        $model->customer_id = $customer_id;
        $model->customer_name = $customer->name;

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new AddressService())->create($model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Address successfully created'));
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(['index', 'customer_id' => $customer_id]);
        }

        return $this->renderAjax('_create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $address = $this->findModel($id);
        $model = new AddressUpdateForm($address);

        if ($model->load(Yii::$app->request->post())){
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new AddressService())->update($address, $model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Address successfully updated'));
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('_update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Address
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Address
    {
        if (($model = Address::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
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
