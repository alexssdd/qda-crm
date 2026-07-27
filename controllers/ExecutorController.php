<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\search\ExecutorSearch;
use app\core\helpers\UserHelper;
use app\core\helpers\PhoneHelper;
use yii\web\NotFoundHttpException;
use app\modules\order\models\Executor;

class ExecutorController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserHelper::ROLE_OPERATOR],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'phone' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel = new ExecutorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionPhone(int $id): array
    {
        $executor = $this->getExecutor($id);

        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->headers->set('Cache-Control', 'no-store, private');
        Yii::$app->response->headers->set('Pragma', 'no-cache');

        return [
            'phone' => PhoneHelper::getMaskPhone($executor->phone),
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    private function getExecutor(int $id): Executor
    {
        $executor = Executor::findOne($id);
        if ($executor !== null) {
            return $executor;
        }

        throw new NotFoundHttpException(
            Yii::t('app', 'The requested executor with {id} does not exist.', ['id' => $id])
        );
    }
}
