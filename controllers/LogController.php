<?php

namespace app\controllers;

use Yii;
use app\entities\Log;
use yii\web\Controller;
use app\search\LogSearch;
use yii\filters\AccessControl;
use app\core\helpers\UserHelper;
use yii\web\NotFoundHttpException;

/**
 * LogController implements the CRUD actions for Log model.
 */
class LogController extends Controller
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
        $searchModel = new LogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        return $this->renderPartial('_view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $id
     * @return Log|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?Log
    {
        if (($model = Log::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
