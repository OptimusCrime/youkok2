<?php
namespace Youkok\Rest\Endpoints;

use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Http\Request;
use Slim\Http\Response;

use Youkok\Biz\Exceptions\InvalidLoginAttemptException;
use Youkok\Biz\Services\Auth\AuthService;

class LoginEndpoint extends BaseRestEndpoint
{
    private AuthService $authService;
    private RouterInterface $router;

    public function __construct(ContainerInterface $container)
    {
        $this->authService = $container->get(AuthService::class);
        $this->router = $container->get('router');
    }

    public function post(Request $request, Response $response): Response
    {
        try {
            // The validate method throws exceptions for all invalid login attempts
            $this->authService->validateLogin($request->getParams());

            $this->authService->setAdminCookie();

            return $response
                ->withStatus(302)
                ->withHeader('Location', $this->router->pathFor('admin_home'));

        } catch (InvalidLoginAttemptException $e) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', 'https://www.google.com');
        }
    }
}
