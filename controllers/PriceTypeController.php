<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use yii\web\Controller;
use app\entities\PriceType;
use yii\filters\AccessControl;
use app\search\PriceTypeSearch;
use app\core\helpers\UserHelper;
use app\forms\PriceTypeCreateForm;
use app\forms\PriceTypeUpdateForm;
use app\services\PriceTypeService;
use yii\web\NotFoundHttpException;

/**
 * Price type controller
 */
class PriceTypeController extends Controller
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
                        'roles' => [UserHelper::ROLE_ADMIN],
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
        $searchModel = new PriceTypeSearch();
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
        $model = new PriceTypeCreateForm();

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new PriceTypeService())->create($model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Price type successfully created'));
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
        $priceType = $this->findModel($id);
        $model = new PriceTypeUpdateForm($priceType);

        if ($model->load(Yii::$app->request->post())){
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new PriceTypeService())->update($priceType, $model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Price type successfully updated'));
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
     * @return PriceType
     * @throws NotFoundHttpException
     */
    protected function findModel($id): PriceType
    {
        if (($model = PriceType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
