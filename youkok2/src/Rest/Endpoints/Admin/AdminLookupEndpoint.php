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
use Youkok\Biz\Services\CoursesLookupService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminLookupEndpoint extends BaseRestEndpoint
{
    private CoursesLookupService $coursesLookupService;
    private Logger $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->coursesLookupService = $container->get(CoursesLookupService::class);
        $this->logger = $container->get('logger');
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            return $this->outputJson($response, [
                'data' => $this->coursesLookupService->getCoursesToAdminLookup($routeParser)
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response);
        }
    }
}
