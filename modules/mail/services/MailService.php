<?php

namespace app\modules\mail\services;

use Exception;
use app\services\LogService;
use app\modules\mail\MailApi;
use app\core\helpers\LogHelper;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Mail service
 */
class MailService
{
    /**
     * @param $to
     * @param $subject
     * @param $text
     * @return void
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function sendMessage($to, $subject, $text): void
    {
        $target = LogHelper::TARGET_MAIL_SEND_MESSAGE;

        try {
            $api = $this->getApi();
            $api->sendMessage($to, $subject, $text);

            // Log
            LogService::success($target, ['to' => $to, 'subject' => $subject, 'text' => $text]);
        } catch (Exception $e){
            LogService::error($target, ['to' => $to, 'subject' => $subject, 'text' => $text, 'error' => $e->getMessage()]);

            throw $e;
        }
    }

    /**
     * @return MailApi
     */
    protected function getApi(): MailApi
    {
        return (new MailApi());
    }
}