<?php
namespace Youkok\Common\Containers;

use DI\Container;
use Exception;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Youkok\Helpers\Configuration\Configuration;

class InternalServerError
{
    public static function load(Container $container): void
    {
        $container->set(InternalServerError::class, function (Request $request, Response $response, Exception $exception): Response {
            $configuration = Configuration::getInstance();
            if ($configuration->isDev()) {
                var_dump(get_class($exception));
                var_dump($exception->getMessage());
                var_dump($exception->getTraceAsString());
                die();
            }

            return $response->withStatus(500);
        });
    }
}
