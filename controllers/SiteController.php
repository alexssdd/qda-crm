<?php

namespace app\controllers;

use Yii;
use Throwable;
use yii\helpers\Url;
use DomainException;
use yii\web\Response;
use app\services\UserService;
use app\core\helpers\UserHelper;
use app\modules\auth\enums\AuthMethod;
use app\modules\auth\forms\LoginOtpForm;
use app\modules\auth\models\AuthIdentity;
use app\modules\auth\forms\LoginStartForm;
use app\modules\auth\forms\LoginPasswordForm;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex(): Response
    {
        $user = UserHelper::getIdentity();

        if ($user->role == UserHelper::ROLE_MARKETING){
            return $this->redirect(['advert/index']);
        } else {
            return $this->redirect(['order/index']);
        }
    }

    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'empty';
        $model = new LoginStartForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $model->getUser();

            $hasPassword = AuthIdentity::find()
                ->andWhere([
                    'user_id' => $user->id,
                    'type' => AuthMethod::PASSWORD->value
                ])
                ->exists();

            if ($hasPassword) {
                return $this->asJson([
                    'success' => true,
                    'html' => $this->renderAjax('_login_password', [
                        'model' => new LoginPasswordForm(),
                        'phone' => $user->phone
                    ])
                ]);
            }

            return $this->asJson([
                'success' => true,
                'html' => $this->renderAjax('_login_otp', [
                    'model' => new LoginOtpForm(),
                    'phone' => $user->phone
                ])
            ]);
        }

        return $this->render('login-start', ['model' => $model]);
    }

    public function actionLoginPassword(): Response
    {
        $form = new LoginPasswordForm();
        $form->load(Yii::$app->request->post());

        try {
            if (!$form->login()) {
                throw new DomainException($form->getErrorMessage());
            }

            return $this->asJson([
                'success' => true,
                'redirect' => Url::to(['site/index'])
            ]);
        } catch (DomainException $e) {
            return $this->asJson([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        } catch (Throwable $e) {
            return $this->asJson([
                'success' => false,
                'error' => 'Internal server error'
            ]);
        }
    }

    public function actionLoginOtp(): Response
    {
        $form = new LoginOtpForm();
        $form->load(Yii::$app->request->post());

        try {
            if (!$form->login()) {
                throw new DomainException($form->getErrorMessage());
            }

            return $this->asJson([
                'success' => true,
                'redirect' => Url::to(['site/index'])
            ]);
        } catch (DomainException $e) {
            return $this->asJson([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        } catch (Throwable $e) {
            return $this->asJson([
                'success' => false,
                'error' => 'Internal server error'
            ]);
        }
    }

    public function actionLogout(): Response
    {
        // State offline
        (new UserService())->stateOffline(UserHelper::getIdentity());

        Yii::$app->user->logout();

        return $this->goHome();
    }
}
