<?php

namespace Youkok\Rest\Endpoints;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Common\Utilities\CacheKeyGenerator;

class FrontpageEndpoint extends BaseRestEndpoint
{
    /** @var FrontpageService */
    private $frontpageService;

    /** @var CourseMapper */
    private $courseMapper;

    /** @var ElementMapper */
    private $elementMapper;

    /** @var Logger */
    private $logger;

    /** @var CacheService */
    private $cacheService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->frontpageService = $container->get(FrontpageService::class);
        $this->courseMapper = $container->get(CourseMapper::class);
        $this->elementMapper = $container->get(ElementMapper::class);
        $this->logger = $container->get(Logger::class);
        $this->cacheService = $container->get(CacheService::class);
    }

    public function boxes(Request $request, Response $response): Response
    {
        $payload = $this->frontpageService->boxes();

        return $this->outputJson($response, [
            'data' => $payload
        ]);
    }

    public function popularElements(Request $request, Response $response): Response
    {
        // TODO: Enum?
        $delta = $request->params('delta') ?? 'all';

        try {
            $payload = $this->frontpageService->popularElements($delta);

            return $this->outputJson($response, [
                'elements' => $this->mapElementsMostPopular($payload),
                'preference' => $delta
            ]);
        } catch (ElementNotFoundException | GenericYoukokException $ex) {
            $this->logger->warning($ex);

            return $response->withStatus(400);
        }
    }

    public function popularCourses(Request $request, Response $response): Response
    {
        // TODO: Enum?
        $delta = $request->params('delta') ?? 'all';

        try {
            $payload = $this->frontpageService->popularCourses($delta);

            return $this->outputJson($response, [
                'courses' => $this->mapCoursesMostPopular($payload),
                'preference' => $delta
            ]);
        } catch (ElementNotFoundException | GenericYoukokException $ex) {
            $this->logger->error($ex);

            return $response->withStatus(400);
        }
    }

    public function newest(Request $request, Response $response): Response
    {
        $cacheKey = CacheKeyGenerator::keyForNewestElementsPayload();
        $cache = $this->fetchFromRedisCache($cacheKey);
        if ($cache !== null) {
            return $this->outputJson($response, [
                'data' => $cache
            ]);
        }

        $payload = $this->frontpageService->newest();
        $data = $this->elementMapper->mapFromArray(
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
    }

    public function lastVisited(Request $request, Response $response): Response
    {
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
    }

    public function lastDownloaded(Request $request, Response $response): Response
    {
        $cacheKey = CacheKeyGenerator::keyForLastDownloadedPayload();
        $cache = $this->fetchFromRedisCache($cacheKey);
        if ($cache !== null) {
            return $this->outputJson($response, [
                'data' => $cache
            ]);
        }

        $payload = $this->frontpageService->lastDownloaded();

        $data = $this->elementMapper->mapFromArray(
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
    }

    public function put(Request $request, Response $response): Response
    {
        $params = json_decode($request->getBody(), true);

        $delta = $params[FrontpageService::FRONTPAGE_PUT_DELTA_PARAM] ?? null;
        $value = $params[FrontpageService::FRONTPAGE_PUT_VALUE_PARAM] ?? null;

        if (!is_string($delta) || !is_string($value)) {
            return $response->withStatus(400);
        }

        try {
            $output = $this->frontpageService->put($delta, $value);

            return $this->outputJson(
                $response,
                $this->mapUpdateMostPopular($output, $delta, $value)
            );
        } catch (InvalidRequestException $e) {
            $this->logger->info('Got invalid frontpage put request. Delta: ' . $delta . '. Value: ' . $value);

            return $response->withStatus(400);
        }
    }

    private function mapElementsMostPopular($arr): array
    {
        return $this->elementMapper->mapFromArray(
            $arr,
            [
                ElementMapper::DATASTORE_DOWNLOADS,
                ElementMapper::PARENT_DIRECT,
                ElementMapper::PARENT_COURSE
            ]
        );
    }

    private function mapCoursesMostPopular($arr)
    {
        return $this->courseMapper->map(
            $arr,
            [
                CourseMapper::DATASTORE_DOWNLOAD_ESTIMATE
            ]
        );
    }

    private function mapUpdateMostPopular($output, $delta, $value)
    {
        $ret = [
            'preference' => $value,
        ];

        // TODO fix this
        if ($delta === "most_popular_element") {
            $ret['elements'] = $this->mapElementsMostPopular($output);
        } else {
            $ret['courses'] = $this->mapCoursesMostPopular($output);
        }

        return $ret;
    }

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
