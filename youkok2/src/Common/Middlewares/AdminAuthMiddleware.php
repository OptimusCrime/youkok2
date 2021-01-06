<?php
namespace Youkok\Common\Middlewares;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

use Youkok\Biz\Exceptions\InsufficientAccessException;
use Youkok\Biz\Exceptions\InvalidLoginAttemptException;
use Youkok\Biz\Services\Auth\AuthService;
use Youkok\Common\Utilities\CookieHelper;

class AdminAuthMiddleware
{
    private AuthService $authService;

    public function __construct(ContainerInterface $container)
    {
        $this->authService = $container->get(AuthService::class);
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        try {
            $this->authService->validateCookie($request);
        } catch (InsufficientAccessException $e) {
            return static::noAccess($response);
        }

        return static::noAccess($response);
    }

    private static function noAccess(Response $response)
    {
        return $response->withStatus(403);
    }
}
