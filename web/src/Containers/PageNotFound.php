<?php
namespace Youkok\Containers;

use \Slim\Container;

use Youkok\Views\PageNotFound as PageNotFoundView;

class PageNotFound
{
    public static function load(Container $container)
    {
        $container['notFoundHandler'] = function ($c) {
            return function ($request, $response) use ($c) {
                $pageNotFound = new PageNotFoundView($c);
                return $pageNotFound->view($request, $response);
            };
        };
    }
}


