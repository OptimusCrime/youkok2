<?php
namespace Youkok\Common\Middlewares;

use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Youkok\Helpers\Configuration\Configuration;

class ReverseProxyMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        $scheme = Configuration::getInstance()->isSSL() ? 'https' : 'http';
        $uri = $request->getUri()->withScheme($scheme);
        $request = $request->withUri($uri);

        return $requestHandler->handle($request);
    }
}
