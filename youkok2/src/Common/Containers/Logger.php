<?php
namespace Youkok\Common\Containers;

use Closure;
use Monolog\Formatter\LineFormatter;
use Psr\Container\ContainerInterface;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    public static function load(ContainerInterface $container): void
    {
        $container[MonologLogger::class] = function (): MonologLogger {
            $logger = new MonologLogger(getenv('LOGGER_NAME'));

            $formatter = new LineFormatter(LineFormatter::SIMPLE_FORMAT, LineFormatter::SIMPLE_DATE);
            $formatter->includeStacktraces(true);

            $stream = new StreamHandler(
                getenv('LOGS_DIRECTORY') . getenv('LOGGER_FILE'),
                getenv('DEV') === '1' ? MonologLogger::ERROR : MonologLogger::INFO,
                true,
                0775
            );
            $stream->setFormatter($formatter);

            $logger->pushHandler($stream);

            return $logger;
        };
    }
}
