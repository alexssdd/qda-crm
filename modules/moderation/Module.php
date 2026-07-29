<?php

namespace app\modules\moderation;

use Yii;
use yii\base\Module as BaseModule;
use yii\console\Application as ConsoleApplication;
use app\services\ConfigService;
use app\modules\moderation\clients\OpenAiResponsesClient;
use app\modules\moderation\services\ModerationMessageBuilder;
use app\modules\moderation\services\OrderContextBuilder;
use app\modules\moderation\services\OrderModerationService;

final class Module extends BaseModule
{
    private const CONFIG_GROUP = 'openai';

    public $controllerNamespace = 'app\modules\moderation\controllers';

    public string $model = 'gpt-5.6-luna';
    public string $apiBaseUrl = 'https://api.openai.com/v1';
    public int $timeoutSeconds = 30;
    public int $maxAttempts = 3;
    public int $batchSize = 20;
    public int $lookbackDays = 7;
    public int $recentOrdersLimit = 5;
    public string $promptVersion = 'order-moderation-v1';

    private ?OrderModerationService $orderModerationService = null;

    public function init(): void
    {
        parent::init();

        if (Yii::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'app\modules\moderation\commands';
        }
    }

    public function getOrderModerationService(): OrderModerationService
    {
        if ($this->orderModerationService === null) {
            $config = Yii::$container->get(ConfigService::class);
            // Global and CRM share the DB, but their FileCache instances are local.
            // Always read the current OpenAI credentials at command startup.
            $config->clear(self::CONFIG_GROUP);

            $client = new OpenAiResponsesClient(
                $config,
                $this->apiBaseUrl,
                $this->model,
                $this->timeoutSeconds,
                $this->maxAttempts,
                $this->promptVersion,
            );
            $client->assertConfigured();

            $this->orderModerationService = new OrderModerationService(
                new OrderContextBuilder($this->recentOrdersLimit),
                $client,
                new ModerationMessageBuilder(),
                $this->promptVersion,
            );
        }

        return $this->orderModerationService;
    }
}
