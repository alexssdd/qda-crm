<?php

namespace app\controllers;

use Yii;
use Exception;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\core\helpers\UserHelper;
use app\search\dashboard\ChannelSearch;

/**
 * Class DashboardController
 * @package app\controllers
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
                        'roles' => [UserHelper::ROLE_OPERATOR],
                    ]
                ],
            ],

        ];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionChannelExport(): string
    {
        $searchModel = new ChannelSearch();
        $data = $searchModel->export();

        return $this->render('channel_export', [
            'data' => $data,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionChannelProduct(): string
    {
        $searchModel = new ChannelSearch([
            'date_range' => date('01.m.Y') . ' 00:00 - ' . date('d.m.Y', strtotime('last day of this month')) . ' 23:59'
        ]);
        $data = $searchModel->product(Yii::$app->request->queryParams);

        return $this->render('channel_product', [
            'data' => $data,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * @return string
     */
    public function actionChannelSale(): string
    {
        return $this->render('channel_sale');
    }
}