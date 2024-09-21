<?php

namespace Youkok\Common\Containers;

use DI\Container;
use Exception;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Youkok\Web\Views\PageNotFound as PageNotFoundView;

class PageNotFound
{
    public static function load(Container $container): void
    {
        $container->set(PageNotFound::class, function (Request $request, Response $response, Exception $_): Response {
            $pageNotFound = new PageNotFoundView();
            return $pageNotFound->view($request, $response);
        });
    }
}
