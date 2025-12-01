<?php

namespace app\controllers;

use Yii;
use Exception;
use yii\web\Response;
use app\forms\LoginForm;
use app\services\UserService;
use app\core\helpers\UserHelper;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        $user = UserHelper::getIdentity();

        if ($user->role == UserHelper::ROLE_MARKETING){
            return $this->redirect(['advert/index']);
        } else {
            return $this->redirect(['order/index']);
        }
    }

    /**
     * @return string
     */
    public function actionDemo(): string
    {
        return $this->render('demo');
    }

    /**
     * @return string|Response
     * @throws Exception
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'empty';
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // State online
            (new UserService())->stateOnline(UserHelper::getIdentity());

            return $this->redirect(['site/index']);
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function actionLogout(): Response
    {
        // State offline
        (new UserService())->stateOffline(UserHelper::getIdentity());

        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionStateOnline(): void
    {
        // State online
        (new UserService())->stateOnline(UserHelper::getIdentity());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionStateOffline(): void
    {
        // State offline
        (new UserService())->stateOffline(UserHelper::getIdentity());
    }
}
