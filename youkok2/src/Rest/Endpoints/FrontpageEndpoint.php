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
use Youkok\Biz\Exceptions\InvalidValueException;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\PopularListing\MostPopularCoursesService;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class FrontpageEndpoint extends BaseRestEndpoint
{
    const int POPULAR_MAX_LIMIT = 10;

    private FrontpageService $frontpageService;
    private MostPopularElementsService $mostPopularElementsService;
    private MostPopularCoursesService $mostPopularCoursesService;
    private Logger $logger;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->frontpageService = $container->get(FrontpageService::class);
        $this->mostPopularElementsService = $container->get(MostPopularElementsService::class);
        $this->mostPopularCoursesService = $container->get(MostPopularCoursesService::class);
        $this->logger = $container->get('logger');
    }

    public function boxes(Request $request, Response $response): Response
    {
        try {
            $payload = $this->frontpageService->boxes();

            return $this->outputJson($response, [
                'data' => $payload
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }

    public function popularElements(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $queryParams = $request->getQueryParams();
            $delta = MostPopularElement::fromValue($queryParams['delta'] ?? MostPopularElement::ALL()->getValue());

            $elements = $this->mostPopularElementsService->getMostPopularElements(
                $routeParser,
                $delta,
                static::POPULAR_MAX_LIMIT
            );

            return $this->outputJson($response, [
                'elements' => $elements,
                'preference' => $delta
            ]);
        } catch (InvalidValueException $ex) {
            $this->logger->debug($ex);
            return $this->returnBadRequest($response);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }

    public function popularCourses(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $queryParams = $request->getQueryParams();
            $delta = MostPopularCourse::fromValue($queryParams['delta'] ?? MostPopularCourse::ALL()->getValue());

            $courses = $this->mostPopularCoursesService->getMostPopularCourses(
                $routeParser,
                $delta,
                static::POPULAR_MAX_LIMIT,
            );

            return $this->outputJson($response, [
                'courses' => $courses,
                'preference' => $delta
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }

    public function newest(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $this->outputJson($response, [
                'data' => $this->frontpageService->getNewestElements($routeParser),
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }

    public function lastVisited(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $this->outputJson($response, [
                'data' => $this->frontpageService->getLastVisitedCurses($routeParser),
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }

    public function lastDownloaded(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $this->outputJson($response, [
                'data' => $this->frontpageService->getLastDownloaded($routeParser),
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }
}
