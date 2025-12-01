<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use yii\web\Controller;
use app\entities\Country;
use app\search\CountrySearch;
use yii\filters\AccessControl;
use app\forms\CountryUpdateForm;
use app\core\helpers\UserHelper;
use app\services\CountryService;
use yii\web\NotFoundHttpException;

/**
 * Country controller
 */
class CountryController extends Controller
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
        $searchModel = new CountrySearch();
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
     */
    public function actionUpdate($id)
    {
        $country = $this->findModel($id);
        $model = new CountryUpdateForm($country);

        if ($model->load(Yii::$app->request->post())){
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new CountryService())->update($country, $model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Country successfully updated'));
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
     * @return Country
     * @throws NotFoundHttpException
     */
    protected function findModel($id): Country
    {
        if (($model = Country::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
