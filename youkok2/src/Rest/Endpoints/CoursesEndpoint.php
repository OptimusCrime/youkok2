<?php
namespace Youkok\Rest\Endpoints;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Slim\Routing\RouteContext;
use Youkok\Biz\Exceptions\IdenticalLookupException;
use Youkok\Biz\Services\CoursesLookupService;

class CoursesEndpoint extends BaseRestEndpoint
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

    public function post(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $queryParams = $request->getQueryParams();
            $checksum = $queryParams['checksum'] ?? null;

            return $this->outputJson(
                $response,
                $this->coursesLookupService->get(
                    $routeParser,
                    $checksum
                )
            );
        } catch (IdenticalLookupException $ex) {
            // Content has not changed
            return $response->withStatus(304);
        } catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnInternalServerError($response);
        }
    }
}
