<?php
namespace Youkok\Common\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

use Youkok\Helpers\Configuration\Configuration;

class ReverseProxyMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $scheme = Configuration::getInstance()->isSSL() ? 'https' : 'http';
        $uri = $request->getUri()->withScheme($scheme);
        $request = $request->withUri($uri);
        return $next($request, $response);
    }
}
