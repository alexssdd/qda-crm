<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use app\entities\Order;
use yii\web\Controller;
use app\services\LogService;
use app\core\hash\HashCrypto;
use app\core\helpers\LogHelper;
use yii\web\NotFoundHttpException;
use app\core\helpers\PaymentHelper;
use app\modules\kaspi\services\KaspiPaymentService;
use app\modules\halyk\services\HalykPaymentService;

/**
 * Payment controller
 */
class PaymentController extends Controller
{
    public $layout = 'empty';

    /**
     * @param $token
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($token): string
    {
        $order = $this->getOrder($token);

        $methods = [
            [
                'type' => PaymentHelper::PAYMENT_KASPI_ONLINE,
                'name' => 'Kaspi Bank',
                'code' => 'kaspi',
                'icon' => '/images/logo_kaspi.svg'
            ],
            [
                'type' => PaymentHelper::PAYMENT_HALYK_ONLINE,
                'name' => 'Halyk Bank',
                'code' => 'halyk',
                'icon' => '/images/logo_halyk.svg'
            ],
            [
                'type' => PaymentHelper::PAYMENT_BCK_ONLINE,
                'name' => 'Банк ЦентрКредит',
                'code' => 'bck',
                'icon' => '/images/logo_bck.svg'
            ],
        ];

        return $this->render('index', [
            'token' => $token,
            'order' => $order,
            'methods' => $methods
        ]);
    }

    /**
     * @param $code
     * @param $token
     * @return string
     */
    public function actionView($code, $token): string
    {
        $methods = [];
        switch ($code) {
            case PaymentHelper::PROVIDER_HALYK_CODE:
                $methods = [
                    [
                        'name' => 'Картой Halyk Bank',
                        'code' => 'halyk',
                        'type' => 10,
                        'fee' => 0,
                        'icon' => '/images/logo_halyk.svg'
                    ],
                    [
                        'name' => 'Картой другого банка',
                        'code' => 'halyk',
                        'type' => 11,
                        'fee' => 0,
                        'icon' => '/images/logo_bank.png'
                    ],
                    [
                        'name' => 'Картой Halyk Bank юр лицо',
                        'code' => 'halyk',
                        'type' => 12,
                        'fee' => 500,
                        'icon' => '/images/logo_halyk.svg'
                    ]
                ];
                break;
            case PaymentHelper::PROVIDER_KASPI_CODE:
                $methods = [
                    [
                        'name' => 'Картой Kaspi Bank',
                        'code' => 'kaspi',
                        'type' => 10,
                        'fee' => 0,
                        'icon' => '/images/logo_kaspi.svg'
                    ],
                    [
                        'name' => 'Картой Kaspi Bank юр лицо',
                        'code' => 'kaspi',
                        'type' => 12,
                        'fee' => 0,
                        'icon' => '/images/logo_kaspi.svg'
                    ]
                ];
                break;
        }

        return $this->render('view', [
            'token' => $token,
            'methods' => $methods
        ]);
    }

    /**
     * @param $code
     * @param $token
     * @param $type
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionWidget($code, $token, $type)
    {
        $order = $this->getOrder($token);
        $target = LogHelper::TARGET_PAYMENT_WIDGET;
        $data = [];
        $params = [];
        $viewName = null;

        try {
            switch ($code) {
                case PaymentHelper::PROVIDER_HALYK_CODE:
                    $service = new HalykPaymentService($order);
                    $data = $service->getData();
                    $params = $service->getParams();
                    $viewName = 'halyk';
                    break;
                case PaymentHelper::PROVIDER_KASPI_CODE:
                    $service = new KaspiPaymentService($order);
                    $data = $service->getData();
                    $viewName = 'kaspi';
                    return Yii::$app->response->redirect('https://kaspi.kz/online?' . http_build_query($data));
            }

            if (!$data || !$viewName) {
                throw new DomainException("Payment data empty");
            }

            return $this->render($viewName, [
                'data' => $data,
                'params' => $params
            ]);
        } catch (Exception $e) {
            LogService::error($target, ['number' => $order->number, 'error' => $e->getMessage()]);
            Yii::$app->session->setFlash('error', Yii::t('app', 'Payment widget init error'));

            return $this->redirect(['payment/view', 'code' => $code, 'token' => $token]);
        }
    }

    /**
     * @param $token
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSuccess($token): string
    {
        $order = $this->getOrder($token);

        return $this->render('success');
    }

    /**
     * @param $token
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionFailure($token): string
    {
        $order = $this->getOrder($token);

        return $this->render('failure');
    }

    /**
     * @param $token
     * @return Order
     * @throws NotFoundHttpException
     */
    protected function getOrder($token): Order
    {
        $number = (new HashCrypto())->extract($token);

        if (!$order = Order::findOne(['number' => $number])) {
            throw new NotFoundHttpException("Page not found");
        }
        return $order;
    }
}