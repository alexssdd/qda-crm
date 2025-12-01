<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use yii\web\Controller;
use app\entities\Advert;
use app\search\AdvertSearch;
use yii\filters\AccessControl;
use app\forms\AdvertCreateForm;
use app\forms\AdvertUpdateForm;
use app\services\AdvertService;
use app\core\helpers\UserHelper;
use yii\web\NotFoundHttpException;
use vova07\imperavi\actions\UploadFileAction;

/**
 * Advert controller
 */
class AdvertController extends Controller
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
                        'roles' => [UserHelper::ROLE_MARKETING],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['modal', 'showed'],
                        'roles' => [UserHelper::ROLE_OPERATOR],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function actions(): array
    {
        return [
            'image-upload' => [
                'class' => UploadFileAction::class,
                'url' => Yii::$app->params['domainCdn'] . 'advert/',
                'path' => Yii::getAlias('@storage/web/advert'),
            ],
            'file-upload' => [
                'class' => UploadFileAction::class,
                'url' => Yii::$app->params['domainCdn'] . 'advert_files/',
                'path' => Yii::getAlias('@storage/web/advert_files'),
                'uploadOnlyImage' => false,
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new AdvertSearch();
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
        $model = new AdvertCreateForm();
        $model->begin_at = date('d.m.Y H:i', strtotime('+5 minutes'));
        $model->end_at = date('d.m.Y H:i', strtotime('+1 month'));

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new AdvertService())->create($model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Advert successfully created'));
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
        $advert = $this->findModel($id);
        $model = new AdvertUpdateForm($advert);

        if ($model->load(Yii::$app->request->post())){
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new AdvertService())->update($advert, $model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Advert successfully updated'));
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
     * @return Response
     */
    public function actionModal(): Response
    {
        $user = UserHelper::getIdentity();
        $advert = (new AdvertService())->getActiveAdvert($user);

        if (!$advert){
            return $this->asJson([
                'status' => 'empty'
            ]);
        }

        return $this->asJson([
            'status' => 'success',
            'id' => $advert->id,
            'content' => $this->renderAjax('_modal', [
                'advert' => $advert
            ])
        ]);
    }

    /**
     * @param $id
     * @return void
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionShowed($id)
    {
        $model = $this->findModel($id);
        $user = UserHelper::getIdentity();

        (new AdvertService())->setShowed($model, $user);
    }

    /**
     * @param $id
     * @return Advert
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Advert
    {
        if (($model = Advert::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
