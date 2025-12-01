<?php

namespace app\services;

use vova07\console\ConsoleRunner;

/**
 * Class ConsoleService
 * @package app\services
 */
class ConsoleService
{
    /**
     * @return ConsoleRunner
     */
    public function getClient(): ConsoleRunner
    {
        return new ConsoleRunner(['file' => '@app/yii']);
    }

    /**
     * @param $cmd
     * @param array $params
     * @return void
     */
    public function run($cmd, array $params = [])
    {
        $client = $this->getClient();

        if ($params){
            $cmd .= ' ' . implode(' ', $params);
        }

        $client->run($cmd);
    }

    /**
     * @param $cmd
     * @param array $params
     * @return void
     */
    public function runApi($cmd, array $params = []): void
    {
        $client = new ConsoleRunner(['file' => '/home/app/web/api.servicemarwin.com/public_html/yii']);

        if ($params){
            $cmd .= ' ' . implode(' ', $params);
        }

        $client->run($cmd);
    }
}