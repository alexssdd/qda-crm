<?php

namespace app\core\bootstrap;

use Yii;
use yii\base\BootstrapInterface;

class LanguageSelector implements BootstrapInterface
{
    public array $supportedLanguages = ['en', 'kk', 'ru', 'uz'];

    public function bootstrap($app): void
    {
        $language = Yii::$app->request->getHeaders()->get('App-Language');

        if ($language && in_array($language, $this->supportedLanguages)) {
            Yii::$app->language = $language;
        }
    }
}