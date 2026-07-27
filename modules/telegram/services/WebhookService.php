<?php

namespace app\modules\telegram\services;

use DomainException;
use app\entities\Customer;
use app\modules\telegram\forms\WebhookForm;

class WebhookService
{
    public function handle(WebhookForm $form)
    {
        // todo added norm logic
        $values = preg_split('/\s+/', $form->text, 2);
        if (($values[0] ?? null) === '/start' && isset($values[1])) {
            $uuid = trim($values[1]);
            if (preg_match('/^[a-zA-Z0-9_-]{1,128}$/', $uuid)) {
                $this->invited($uuid, (int) $form->chat->id);
            }
        }
    }

    protected function invited($uuid, $chatId)
    {
        /** @var Customer $customer */
        $customer = Customer::find()
            ->andWhere("JSON_EXTRACT(config, '$.telegram_invite') = :uuid", ['uuid' => $uuid])
            ->one();

        if (!$customer) {
            return;
        }

        $config = $customer->config;
        $config['telegram_id'] = $chatId;
        $customer->config = $config;

        if (!$customer->save(false)) {
            throw new DomainException("Customer set telegram_id error");
        }

        $message = 'Клиент: ' . $customer->name . ', Вы успешно авторизировались';

        (new TelegramService())->send($chatId, $message);
    }
}
