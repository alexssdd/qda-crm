<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\core\helpers\UserHelper;
use app\search\dashboard\DashboardSearch;

/**
 * Dashboard controller
 */
class DashboardController extends Controller
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
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new DashboardSearch();
        $searchModel->prepare(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'stats' => $searchModel->stats(),
            'charts' => $searchModel->charts(),
            'coverage' => $searchModel->coverage(),
        ]);
    }
}
