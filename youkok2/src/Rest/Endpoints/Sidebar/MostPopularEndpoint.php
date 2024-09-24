<?php
namespace Youkok\Rest\Endpoints\Sidebar;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Slim\Routing\RouteContext;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Enums\MostPopularElement;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class MostPopularEndpoint extends BaseRestEndpoint
{
    const int SERVICE_LIMIT = 10;

    private MostPopularElementsService $mostPopularElementsService;
    private Logger $logger;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->mostPopularElementsService = $container->get(MostPopularElementsService::class);
        $this->logger = $container->get('logger');
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            $elements = $this->mostPopularElementsService->getMostPopularElements(
                $routeParser,
                MostPopularElement::WEEK(),
                static::SERVICE_LIMIT
            );

            return $this->outputJson($response, [
                'data' => $elements,
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnInternalServerError($response);
        }
    }
}
