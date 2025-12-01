<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use app\entities\Store;
use yii\web\Controller;
use app\search\StoreSearch;
use app\forms\StoreCreateForm;
use app\forms\StoreUpdateForm;
use app\services\StoreService;
use yii\filters\AccessControl;
use app\core\helpers\UserHelper;
use yii\web\NotFoundHttpException;

/**
 * Store controller
 */
class StoreController extends Controller
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
                        'actions' => ['index', 'view']
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
        $searchModel = new StoreSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new StoreCreateForm();

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new StoreService())->create($model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Store successfully created'));
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(['index']);
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
        $store = $this->findModel($id);
        $model = new StoreUpdateForm($store);

        if ($model->load(Yii::$app->request->post())){
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new StoreService())->update($store, $model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Store successfully updated'));
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
     * @throws Exception
     */
    public function actionView($id): string
    {
        $store = $this->findModel($id);
        $model = new StoreUpdateForm($store);

        return $this->renderAjax('_view', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Store
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Store
    {
        if (($model = Store::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
