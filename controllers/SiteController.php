<?php

namespace app\controllers;

use Yii;
use Throwable;
use DomainException;
use yii\helpers\Url;
use yii\web\Response;
use app\services\ConsoleService;
use app\modules\auth\forms\LoginForm;
use app\modules\auth\enums\AuthMethod;
use app\modules\auth\forms\LoginOtpForm;
use app\modules\auth\models\AuthIdentity;
use app\modules\auth\forms\LoginPasswordForm;

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

    public function actionIndex(): string
    {
        return $this->render('index');
    }

    public function actionLogin(): Response|string
    {
        $this->layout = 'empty';
        $form = new LoginForm();

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $user = $form->getUser();
            $hasOtp = false;

            if ($user) {
                $hasOtp = AuthIdentity::find()
                    ->andWhere([
                        'user_id' => $user->id,
                        'type' => AuthMethod::OTP->value
                    ])
                    ->exists();
            }

            if ($hasOtp) {
                (new ConsoleService())->run('auth/otp/request', [$user->phone, Yii::$app->language]);
                return $this->asJson([
                    'success' => true,
                    'html' => $this->renderAjax('_login_otp', [
                        'model' => new LoginOtpForm(),
                        'phone' => $user->phone
                    ])
                ]);
            }

            return $this->asJson([
                'success' => true,
                'html' => $this->renderAjax('_login_password', [
                    'model' => new LoginPasswordForm(),
                    'phone' => $form->phone
                ])
            ]);
        }

        return $this->render('login', [
            'model' => $form
        ]);
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
            if (!$form->validate()) {
                throw new DomainException($form->getErrorMessage());
            }

            Yii::$app->user->login($form->getIdentity()->user);

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
        Yii::$app->user->logout();

        return $this->goHome();
    }
}