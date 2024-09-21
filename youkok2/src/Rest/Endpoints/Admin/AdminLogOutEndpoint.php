<?php
namespace Youkok\Rest\Endpoints\Admin;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Slim\Routing\RouteContext;
use Youkok\Biz\Services\Auth\AuthService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminLogOutEndpoint extends BaseRestEndpoint
{
    private AuthService $authService;
    private Logger $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->authService = $container->get(AuthService::class);
        $this->logger = $container->get('logger');
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            $this->authService->removeAdminCookie();

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            return $response
                ->withStatus(302)
                ->withHeader('Location', $routeParser->urlFor('home'));
        } catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnInternalServerError($response);
        }
   }
}
