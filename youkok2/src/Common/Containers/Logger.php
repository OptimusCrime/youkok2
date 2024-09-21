<?php
namespace Youkok\Common\Containers;

use DI\Container;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Logger as MonologLogger;
use Monolog\Level as MonologLevel;
use Monolog\Handler\StreamHandler;

class Logger
{
    public static function load(Container $container): void
    {
        $logger = new MonologLogger('Youkok2');

        $formatter = new LineFormatter(LineFormatter::SIMPLE_FORMAT, NormalizerFormatter::SIMPLE_DATE);
        $formatter->includeStacktraces();

        $stream = new StreamHandler(
            'php://stdout',
            MonologLevel::Debug
        );

        $stream->setFormatter($formatter);

        $logger->pushHandler($stream);

        $container->set('logger', $logger);
    }
}
