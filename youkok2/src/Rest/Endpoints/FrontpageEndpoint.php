<?php

namespace Youkok\Rest\Endpoints;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;
use Slim\Interfaces\RouteParserInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Slim\Routing\RouteContext;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Enums\MostPopularCourse;
use Youkok\Enums\MostPopularElement;

class FrontpageEndpoint extends BaseRestEndpoint
{
    private FrontpageService $frontpageService;
    private CourseMapper $courseMapper;
    private ElementMapper $elementMapper;
    private Logger $logger;
    private CacheService $cacheService;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->frontpageService = $container->get(FrontpageService::class);
        $this->courseMapper = $container->get(CourseMapper::class);
        $this->elementMapper = $container->get(ElementMapper::class);
        $this->logger = $container->get('logger');
        $this->cacheService = $container->get(CacheService::class);
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
            $delta = MostPopularElement::fromValue($queryParams['delta'] ?? 'ALL');
            $payload = $this->frontpageService->popularElements($delta);

            return $this->outputJson($response, [
                'elements' => $this->mapElementsMostPopular($routeParser, $payload),
                'preference' => $delta
            ]);
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
            $delta = MostPopularCourse::fromValue($queryParams['delta'] ?? 'ALL');

            $payload = $this->frontpageService->popularCourses($delta);

            return $this->outputJson($response, [
                'courses' => $this->mapCoursesMostPopular($routeParser, $payload),
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
            $cacheKey = CacheKeyGenerator::keyForNewestElementsPayload();
            $cache = $this->fetchFromRedisCache($cacheKey);

            if ($cache !== null) {
                return $this->outputJson($response, [
                    'data' => $cache
                ]);
            }

            $payload = $this->frontpageService->newest();
            $data = $this->elementMapper->mapFromArray(
                $routeParser,
                $payload,
                [
                    ElementMapper::POSTED_TIME,
                    ElementMapper::PARENT_DIRECT,
                    ElementMapper::PARENT_COURSE
                ]
            );

            $this->cacheService->set(
                $cacheKey,
                json_encode($data)
            );

            return $this->outputJson($response, [
                'data' => $data
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
            $cacheKey = CacheKeyGenerator::keyForLastVisitedCoursesPayload();
            $cache = $this->fetchFromRedisCache($cacheKey);

            if ($cache !== null) {
                return $this->outputJson($response, [
                    'data' => $cache
                ]);
            }

            $payload = $this->frontpageService->lastVisited();

            $data = $this->courseMapper->mapLastVisited($payload);

            $this->cacheService->set(
                $cacheKey,
                json_encode($data)
            );

            return $this->outputJson($response, [
                'data' => $data
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
            $cacheKey = CacheKeyGenerator::keyForLastDownloadedPayload();
            $cache = $this->fetchFromRedisCache($cacheKey);

            if ($cache !== null) {
                return $this->outputJson($response, [
                    'data' => $cache
                ]);
            }

            $payload = $this->frontpageService->lastDownloaded();

            $data = $this->elementMapper->mapFromArray(
                $routeParser,
                $payload,
                [
                    ElementMapper::DOWNLOADED_TIME,
                    ElementMapper::PARENT_DIRECT,
                    ElementMapper::PARENT_COURSE
                ]
            );

            $this->cacheService->set(
                $cacheKey,
                json_encode($data)
            );

            return $this->outputJson($response, [
                'data' => $data
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }

    /**
     * @throws RedisException
     * @throws ElementNotFoundException
     */
    private function mapElementsMostPopular(RouteParserInterface $routeParser, $arr): array
    {
        return $this->elementMapper->mapFromArray(
            $routeParser,
            $arr,
            [
                ElementMapper::DATASTORE_DOWNLOADS,
                ElementMapper::PARENT_DIRECT,
                ElementMapper::PARENT_COURSE
            ]
        );
    }

    /**
     * @throws Exception
     */
    private function mapCoursesMostPopular(RouteParserInterface $routeParser, $arr): array
    {
        return $this->courseMapper->map(
            $routeParser,
            $arr,
            [
                CourseMapper::DATASTORE_DOWNLOAD_ESTIMATE
            ]
        );
    }

    /**
     * @throws RedisException
     */
    private function fetchFromRedisCache(string $key): ?array
    {
        $cache = $this->cacheService->get($key);
        if ($cache === null) {
            return null;
        }

        $data = json_decode($cache, true);
        if (!is_array($data) || empty($data)) {
            return null;
        }

        return $data;
    }
}
