<?php
namespace app\modules\sms\providers;

use Yii;
use DomainException;
use yii\helpers\Json;
use yii\httpclient\Client;
use app\modules\sms\SmsSender;
use app\modules\sms\ModuleTrait;

class SmsC implements SmsSender
{
    use ModuleTrait;

    private $_url;
    private $_login;
    private $_password;
    private $_sender;
    private $_client;

    public function __construct($login = null, $password = null, $url = null, $sender = 'Beezone')
    {
        $this->_login = !$login ? $this->getModule()->getApiAuth()['login'] : $login;
        $this->_password = !$password ? $this->getModule()->getApiAuth()['password'] : $password;
        $this->_url = !$url ? $this->getModule()->getApiUrl() : $url;
        $this->_sender = $sender;
    }

    public function send($phone, $message)
    {
        $response = $this->getClient()
            ->post('/rest/send/', [
                'login' => $this->_login,
                'psw' => $this->_password,
                'phones' => $phone,
                'mes' => $message,
                'sender' => $this->_sender
            ])
            ->setFormat(Client::FORMAT_JSON)
            ->send();

        if (!$response->isOk) {
            throw new DomainException('Response failed');
        }

        return $response->getData();
    }

    public function sendCurl($phone, $message)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->_url . '/rest/send/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => Json::encode([
                'login' => $this->_login,
                'psw' => $this->_password,
                'phones' => $phone,
                'mes' => $message,
                'sender' => $this->_sender,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],

        ]);

        curl_exec($curl);

        if (curl_errno($curl)) {
            throw new DomainException('Couldn\'t send request: ' . curl_error($curl));
        }

        $resultStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($resultStatus !== 200) {
            throw new DomainException('Request failed: HTTP status code: ' . $resultStatus);
        }

        curl_close($curl);
    }

    protected function getClient(): ?Client
    {
        if (!is_object($this->_client)) {
            $this->_client = Yii::createObject([
                'class' => Client::class,
                'baseUrl' => $this->_url,
            ]);
        }
        return $this->_client;
    }
}