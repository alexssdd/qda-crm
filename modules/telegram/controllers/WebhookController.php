<?php

namespace app\modules\telegram\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\JsonParser;
use yii\rest\Controller;
use app\services\LogService;
use yii\filters\AccessControl;
use app\core\helpers\LogHelper;
use app\core\helpers\UserHelper;
use yii\filters\auth\HttpHeaderAuth;
use app\modules\telegram\forms\WebhookForm;
use app\modules\telegram\services\WebhookService;

class WebhookController extends Controller
{
    public function __construct($id, $module, $config = [])
    {
        Yii::$app->request->parsers['application/json'] = ['class' => JsonParser::class];
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpHeaderAuth::class,
            'header' => 'X-Telegram-Bot-Api-Secret-Token'
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [UserHelper::ROLE_SERVICE_TELEGRAM],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        $target = LogHelper::TARGET_TELEGRAM_CALLBACK;
        $body = Yii::$app->request->bodyParams;

        $form = new WebhookForm();
        $form->load($body['message'], '');

        try {
            if (!$form->validate()) {
                throw new DomainException($form->getErrorMessage());
            }

            (new WebhookService())->handle($form);

            LogService::success($target, ['body' => $body]);
            Yii::$app->response->setStatusCode('201');
        } catch (Exception $e) {
            LogService::error($target, ['body' => $body, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}