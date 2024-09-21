<?php
namespace Youkok\Common\Middlewares;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Youkok\Biz\Exceptions\InsufficientAccessException;
use Youkok\Biz\Services\Auth\AuthService;

class AdminAuthMiddleware
{
    private AuthService $authService;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->authService = $container->get(AuthService::class);
    }

    public function __invoke(Request $request, RequestHandlerInterface $requestHandler): Response
    {
        try {
            $this->authService->validateCookie($request);
            return $requestHandler->handle($request);
        } catch (InsufficientAccessException $_) {
            return (new Response())->withStatus(403);
        }
    }
}
