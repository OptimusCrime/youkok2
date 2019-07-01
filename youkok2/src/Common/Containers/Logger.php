<?php
namespace Youkok\Common\Containers;

use Closure;
use Psr\Container\ContainerInterface;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    public static function load(ContainerInterface $container): void
    {
        $container[MonologLogger::class] = function (): MonologLogger {
            $logger = new MonologLogger(getenv('LOGGER_NAME'));

            $stream = new StreamHandler(
                getenv('LOGGER_PATH'),
                getenv('DEV') === '1' ? MonologLogger::DEBUG : MonologLogger::INFO
            );

            $logger->pushHandler($stream);

            return $logger;
        };
    }
}
