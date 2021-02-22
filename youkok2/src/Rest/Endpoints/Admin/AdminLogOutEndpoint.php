<?php
namespace Youkok\Rest\Endpoints\Admin;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Slim\Interfaces\RouterInterface;
use Youkok\Biz\Exceptions\YoukokException;
use Youkok\Biz\Services\Auth\AuthService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminLogOutEndpoint extends BaseRestEndpoint
{
    private AuthService $authService;
    private RouterInterface $router;
    private Logger $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->authService = $container->get(AuthService::class);
        $this->router = $container->get('router');
        $this->logger = $container->get(Logger::class);
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            $this->authService->removeAdminCookie();
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
        }

        return $response
            ->withStatus(302)
            ->withHeader('Location', $this->router->pathFor('home'));
   }
}
