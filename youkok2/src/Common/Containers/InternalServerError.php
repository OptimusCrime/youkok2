<?php
namespace Youkok\Common\Containers;

use Slim\Http\Request;
use Slim\Http\Response;
use Exception;
use Psr\Container\ContainerInterface;

class InternalServerError implements ContainersInterface
{
    public static function load(ContainerInterface $container)
    {
        $container['errorHandler'] = function (ContainerInterface $container) {
            return function (Request $request, Response $response, Exception $exception) use ($container) {
                // TODO
                error_log($exception->getMessage(), 0);
                var_dump($exception->getTraceAsString());
                die();

                return $response->withStatus(500)
                    ->withHeader('Content-Type', 'text/html')
                    ->write(file_get_contents($container->get('settings')['templates_dir'] . 'errors/500.html'));
            };
        };
    }
}


