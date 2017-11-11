<?php
namespace Youkok\Middlewares;

class ReverseProxyMiddleware
{
    public function __invoke($request, $response, $next)
    {
        $scheme = getenv('SSL') === '1' ? 'https' : 'http';
        $uri = $request->getUri()->withScheme($scheme);
        $request = $request->withUri($uri);
        return $next($request, $response);
    }
}
