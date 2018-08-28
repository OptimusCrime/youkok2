<?php
namespace Youkok\Common\Containers;

use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Container\ContainerInterface;

use Youkok\Web\Views\PageNotFound as PageNotFoundView;

class PageNotFound implements ContainersInterface
{
    public static function load(ContainerInterface $container)
    {
        $container['notFoundHandler'] = function (ContainerInterface $container) {
            return function (Request $request, Response $response) use ($container) {
                $pageNotFound = new PageNotFoundView($container);
                return $pageNotFound->view($request, $response);
            };
        };
    }
}


