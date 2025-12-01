<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use app\entities\Brand;
use yii\web\Controller;
use app\search\BrandSearch;
use app\forms\BrandCreateForm;
use app\forms\BrandUpdateForm;
use app\services\BrandService;
use yii\filters\AccessControl;
use app\core\helpers\UserHelper;
use yii\web\NotFoundHttpException;

/**
 * Brand controller
 */
class BrandController extends Controller
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
        $searchModel = new BrandSearch();
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
        $model = new BrandCreateForm();

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new BrandService())->create($model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Brand successfully created'));
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
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        $brand = $this->findModel($id);
        $model = new BrandUpdateForm($brand);

        if ($model->load(Yii::$app->request->post())){
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new BrandService())->update($brand, $model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Brand successfully updated'));
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
        $model = new BrandUpdateForm($store);

        return $this->renderAjax('_view', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Brand
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Brand
    {
        if (($model = Brand::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
