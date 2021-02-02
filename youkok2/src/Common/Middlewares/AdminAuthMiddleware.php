<?php
namespace Youkok\Common\Middlewares;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

use Youkok\Biz\Exceptions\InsufficientAccessException;
use Youkok\Biz\Services\Auth\AuthService;

class AdminAuthMiddleware
{
    private AuthService $authService;

    public function __construct(ContainerInterface $container)
    {
        $this->authService = $container->get(AuthService::class);
    }

    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        try {
            $this->authService->validateCookie($request);
            return $next($request, $response);
        } catch (InsufficientAccessException $e) {
            return static::noAccess($response);
        }
    }

    private static function noAccess(Response $response): Response
    {
        return $response->withStatus(403);
    }
}
