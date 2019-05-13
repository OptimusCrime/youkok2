<?php
namespace Youkok\Common\Containers;

use Slim\Http\Request;
use Slim\Http\Response;
use Exception;
use Psr\Container\ContainerInterface;

class InternalServerError implements ContainersInterface
{
    public static function load(ContainerInterface $container): void
    {
        $container['errorHandler'] = function (ContainerInterface $container) {
            return function (Request $request, Response $response, Exception $exception) use ($container) {
                var_dump(get_class($exception));
                var_dump($exception->getMessage());
                var_dump($exception->getCode());
                var_dump($exception->getTraceAsString());
                die();

                return $response->withStatus(500)
                    ->withHeader('Content-Type', 'text/html')
                    ->write(file_get_contents($container->get('settings')['templates_dir'] . 'errors/500.html'));
            };
        };
    }
}


