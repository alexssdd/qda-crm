<?php

namespace app\core\http;

use yii\httpclient\Client;

final class SensitiveHttpClient extends Client
{
    public function createRequestLogToken($method, $url, $headers, $content): string
    {
        return strtoupper($method) . ' ' . $url . ' [sensitive request redacted]';
    }
}
