<?php
namespace Youkok\Common\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

class ReverseProxyMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $scheme = getenv('SSL') === '1' ? 'https' : 'http';
        $uri = $request->getUri()->withScheme($scheme);
        $request = $request->withUri($uri);
        return $next($request, $response);
    }
}
