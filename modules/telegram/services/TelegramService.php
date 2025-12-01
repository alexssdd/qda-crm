<?php

namespace app\modules\telegram\services;

use Exception;
use DomainException;
use Ramsey\Uuid\Uuid;
use app\entities\Customer;
use app\services\LogService;
use app\modules\telegram\Api;
use app\core\helpers\LogHelper;
use app\modules\sms\providers\SmsC;
use app\modules\sms\services\SmsService;

class TelegramService
{
    public function send($chatId, $message)
    {
        $target = LogHelper::TARGET_TELEGRAM_SEND;

        try {
            $api = $this->getApi();
            $response = $api->send($chatId, $message);

            LogService::success($target, ['data' => ['chat_id' => $chatId, 'message' => $message], 'response' => $response]);
        } catch (Exception $e) {
            LogService::error($target, ['data' => ['chat_id' => $chatId, 'message' => $message], 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function invite($phone)
    {
        $target = LogHelper::TARGET_TELEGRAM_SEND;

        try {
            /** @var Customer $customer */
            $customer = Customer::find()
                ->andWhere([
                    'phone' => $phone
                ])
                ->one();

            if (!$customer) {
                throw new DomainException("Customer phone: $phone not exist");
            }

            $uuid = Uuid::uuid6();
            $customer->config = [
                'telegram_invite' => $uuid
            ];
            $customer->save(false);

            $sms = new SmsService(new SmsC());

            $message = 'Telegram link: https://t.me/marwin_service_bot?start=' . $uuid;
            $sms->send($customer->phone, $message);

            LogService::success($target, ['data' => ['phone' => $phone, 'message' => $message]]);
        } catch (Exception $e) {
            LogService::error($target, ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function getApi(): Api
    {
        return (new Api());
    }
}