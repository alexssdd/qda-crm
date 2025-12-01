<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use yii\web\Controller;
use app\entities\Product;
use app\search\ProductSearch;
use yii\filters\AccessControl;
use app\forms\ProductUpdateForm;
use app\core\helpers\UserHelper;
use app\services\ProductService;
use yii\web\NotFoundHttpException;

/**
 * Product controller
 */
class ProductController extends Controller
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
        $searchModel = new ProductSearch();
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
        $product = $this->findModel($id);
        $model = new ProductUpdateForm($product);

        if ($model->load(Yii::$app->request->post())){
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new ProductService($product))->update($model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Product successfully updated'));
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
        $model = new ProductUpdateForm($store);

        return $this->renderAjax('_view', [
            'model' => $model,
        ]);
    }

    /**
     * @param $sku
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionStock($sku): string
    {
        $model = $this->findModelBySku($sku);
        $stocks = (new ProductService($model))->getStocks();

        return $this->renderPartial('_stock', [
            'model' => $model,
            'stocks' => $stocks
        ]);
    }

    /**
     * @param $id
     * @return Product
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Product
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /**
     * @param $sku
     * @return Product
     * @throws NotFoundHttpException
     */
    protected function findModelBySku($sku): Product
    {
        if (($model = Product::findOne(['sku' => $sku])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
