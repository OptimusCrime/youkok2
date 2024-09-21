<?php
namespace Youkok\Rest\Endpoints;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Slim\Routing\RouteContext;
use Youkok\Biz\Exceptions\InvalidLoginAttemptException;
use Youkok\Biz\Services\Auth\AuthService;

class LoginEndpoint extends BaseRestEndpoint
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

    public function post(Request $request, Response $response): Response
    {
        try {
            // The validate method throws exceptions for all invalid login attempts
            $this->authService->validateLogin($request->getParams());

            $this->authService->setAdminCookie();

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            return $response
                ->withStatus(302)
                ->withHeader('Location', $routeParser->urlFor('admin_home'));

        } catch (InvalidLoginAttemptException $e) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', 'https://www.google.com');
        }
    }
}
