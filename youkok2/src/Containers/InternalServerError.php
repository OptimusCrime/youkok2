<?php
namespace Youkok\Containers;

use \Slim\Container;

class InternalServerError
{
    public static function load(Container $container)
    {
        $container['errorHandler'] = function ($c) {
            return function ($request, $response, $exception) use ($c) {
                error_log($exception->getMessage(), 0);

                return $c['response']->withStatus(500)
                    ->withHeader('Content-Type', 'text/html')
                    ->write(file_get_contents($c->get('settings')['templates_dir'] . 'errors/500.html'));
            };
        };
    }
}


