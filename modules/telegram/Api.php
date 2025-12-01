<?php
namespace app\modules\telegram;

use Yii;
use DomainException;
use yii\httpclient\Client;
use app\services\LogService;
use yii\httpclient\Response;
use app\core\helpers\LogHelper;

class Api
{
    use ModuleTrait;

    private $_url = 'https://api.telegram.org/';
    private $_id;
    private $_client;

    public function __construct()
    {
        // todo move
        $this->_id = '8448869891:AAHMFqOk2LZiy5hTM4moDgYcP-rN7rURJmw';
    }

    public function send($chatId, $message)
    {
        return $this->post('sendMessage', [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'html'
        ]);
    }

    protected function get($url, $data = null)
    {
        $response = $this->getClient()
            ->get($url, $data)
            ->send();

        $this->handleError($response);

        return $response->getData();
    }

    protected function post($url, $data = null)
    {
        $response = $this->getClient()
            ->post($url, $data)
            ->send();

        $this->handleError($response);

        return $response->getData();
    }

    protected function getClient(): ?Client
    {
        if (!is_object($this->_client)) {
            $this->_client = Yii::createObject([
                'class' => Client::class,
                'baseUrl' =>$this->_url . 'bot' . $this->_id . '/',
                'requestConfig' => [
                    'format' => Client::FORMAT_JSON
                ],
                'responseConfig' => [
                    'format' => Client::FORMAT_JSON
                ],
            ]);
        }
        return $this->_client;
    }

    protected function handleError(Response $response): void
    {
        if (!$response->isOk) {
            LogService::error(LogHelper::TARGET_TELEGRAM_SEND, ['response' => $response->getData()]);
            throw new DomainException("Unable get response");
        }
    }
}