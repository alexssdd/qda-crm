<?php

namespace app\services;

use InvalidArgumentException;
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
     * ConsoleRunner отдаёт строку в popen() без экранирования, поэтому маршрут
     * допускается только литеральный, а аргументы всегда проходят escapeshellarg().
     *
     * @param string $cmd
     * @param array $params
     * @return void
     */
    public function run(string $cmd, array $params = []): void
    {
        if (!preg_match('~^[a-z0-9\-]+(/[a-z0-9\-]+)*$~i', $cmd)) {
            throw new InvalidArgumentException('Unsafe console route: ' . $cmd);
        }

        $client = $this->getClient();

        if ($params){
            $cmd .= ' ' . implode(' ', array_map(
                static fn ($param) => escapeshellarg((string) $param),
                $params
            ));
        }

        $client->run($cmd);
    }
}