<?php

namespace app\modules\telegram\commands;

use yii\console\Controller;
use app\modules\telegram\services\TelegramService;

class MainController extends Controller
{
    public function actionSend()
    {
        $chatId = $this->prompt('ChatId:', ['required' => true]);
        $message = $this->prompt('Message:', ['required' => true]);

        (new TelegramService())->send($chatId, $message);
    }
}