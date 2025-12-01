<?php
namespace app\modules\sms\commands;

use yii\console\Controller;
use app\modules\sms\services\SmsService;

class MainController extends Controller
{
    private $_service;

    public function __construct($id, $module, SmsService $service, $config = [])
    {
        $this->_service = $service;
        parent::__construct($id, $module, $config);

    }

    public function actionSend($phone = null, $message = null)
    {
        if (!$phone && !$message) {
            $phone = $this->prompt('Phone:', ['required' => true]);
            $message = $this->prompt('Message:', ['required' => true]);
        }

        $this->_service->send($phone, $message);
    }
}