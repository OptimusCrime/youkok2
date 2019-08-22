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
        // TODO handle rest better!
        $container['errorHandler'] = function () {
            return function (Request $request, Response $response, Exception $exception): Response {
                if (getenv('DEV') === '1') {
                    var_dump(get_class($exception));
                    var_dump($exception->getMessage());
                    var_dump($exception->getCode());
                    var_dump($exception->getTrace());
                    die();
                }

                return $response->withStatus(500)
                    ->withHeader('Content-Type', 'text/html')
                    ->write(@file_get_contents(getenv('TEMPLATE_DIRECTORY') . 'errors/500.html'));
            };
        };
    }
}
