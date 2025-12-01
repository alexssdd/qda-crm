<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use yii\web\Controller;
use app\entities\Customer;
use app\search\CustomerSearch;
use yii\filters\AccessControl;
use app\core\helpers\UserHelper;
use app\forms\CustomerUpdateForm;
use app\services\CustomerService;
use yii\web\NotFoundHttpException;
use app\core\helpers\CustomerHelper;

/**
 * Customer controller
 */
class CustomerController extends Controller
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
                    [
                        'allow' => true,
                        'roles' => [UserHelper::ROLE_OPERATOR],
                        'actions' => ['index', 'detail']
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        $customer = $this->findModel($id);
        $model = new CustomerUpdateForm($customer);

        if ($model->load(Yii::$app->request->post())){
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new CustomerService())->update($customer, $model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Customer successfully updated'));
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
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetail($id): string
    {
        $model = $this->findModel($id);

        return $this->renderAjax('_detail', [
            'model' => $model,
            'detail' => CustomerHelper::getDetail($model)
        ]);
    }

    /**
     * @param $id
     * @return Customer
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Customer
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
