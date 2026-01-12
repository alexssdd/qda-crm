<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use yii\web\Controller;
use app\search\CountrySearch;
use yii\filters\AccessControl;
use app\services\ConsoleService;
use app\forms\CountryUpdateForm;
use app\core\helpers\UserHelper;
use app\services\CountryService;
use yii\web\NotFoundHttpException;
use app\modules\location\models\Country;

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

    public function actionImport(): Response
    {
        (new ConsoleService())->run('location/import/countries');

        Yii::$app->session->setFlash('success', Yii::t('app', 'Country successfully import'));

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionLocations($code): Response
    {
        (new ConsoleService())->run('location/import/locations', [$code]);

        Yii::$app->session->setFlash('success', Yii::t('app', 'Locations successfully import'));

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id): Response|string
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
            'country' => $country
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
