<?php

namespace app\core;

use Yii;
use Exception;
use DomainException;
use yii\base\Component;
use yii\httpclient\Client;
use yii\base\InvalidConfigException;

/**
 * Class YandexTaxiApi
 * @package common\components
 */
class YandexTaxiApi extends Component
{
    public $baseUrl = 'https://b2b.taxi.yandex.net/b2b/cargo/integration';
    public $token;

    private $_httpClient;

    /**
     * @return object|Client
     * @throws InvalidConfigException
     */
    public function getHttpClient()
    {
        if (!is_object($this->_httpClient)) {
            $this->_httpClient = Yii::createObject([
                'class' => Client::class,
                'baseUrl' => $this->baseUrl,
            ]);
        }
        return $this->_httpClient;
    }

    /**
     * @param $data
     * @return mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getPrice($data)
    {
        $response = $this->getHttpClient()
            ->post('v1/check-price', $data, [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
                'Accept-Language' => 'ru-RU'
            ])
            ->setFormat(Client::FORMAT_JSON)
            ->setOptions(['timeout' => 60])
            ->send();

        if (!$response->isOk) {
            if ($response->statusCode == 422) {
                $message = $response->getData()[0]['message'];
            } else {
                $message = $response->getData()['message'];
            }
            throw new DomainException($message);
        }

        return $response->data;
    }
}