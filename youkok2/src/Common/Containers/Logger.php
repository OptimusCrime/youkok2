<?php
namespace Youkok\Common\Containers;

use Closure;
use Monolog\Formatter\LineFormatter;
use Psr\Container\ContainerInterface;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

use Youkok\Helpers\Configuration\Configuration;

class Logger
{
    public static function load(ContainerInterface $container): void
    {
        $container[MonologLogger::class] = function (): MonologLogger {
            $configuration = Configuration::getInstance();

            $logger = new MonologLogger($configuration->getLoggerName());

            $formatter = new LineFormatter(LineFormatter::SIMPLE_FORMAT, LineFormatter::SIMPLE_DATE);
            $formatter->includeStacktraces(true);

            $stream = new StreamHandler(
                'php://stdout',
                static::getLoggerLevel($configuration)
            );

            $stream->setFormatter($formatter);

            $logger->pushHandler($stream);

            return $logger;
        };
    }

    private static function getLoggerLevel(Configuration $configuration): int
    {
        if ($configuration->isDev() || php_sapi_name() === 'cli') {
            return MonologLogger::DEBUG;
        }

        return MonologLogger::NOTICE;
    }
}
