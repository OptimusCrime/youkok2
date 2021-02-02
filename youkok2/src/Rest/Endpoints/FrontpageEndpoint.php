<?php
namespace Youkok\Rest\Endpoints;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidValueException;
use Youkok\Biz\Exceptions\YoukokException;
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

    public function __construct(ContainerInterface $container)
    {
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
        try {
            $delta = MostPopularElement::fromValue($request->getQueryParam('delta', 'ALL'));
            $payload = $this->frontpageService->popularElements($delta);

            return $this->outputJson($response, [
                'elements' => $this->mapElementsMostPopular($payload),
                'preference' => $delta
            ]);
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $response->withStatus(400);
        }
    }

    public function popularCourses(Request $request, Response $response): Response
    {
        try {
            $delta = MostPopularCourse::fromValue($request->getQueryParam('delta', 'ALL'));
            $payload = $this->frontpageService->popularCourses($delta);

            return $this->outputJson($response, [
                'courses' => $this->mapCoursesMostPopular($payload),
                'preference' => $delta
            ]);
        } catch (YoukokException $ex) {
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

        try {
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
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $response->withStatus(400);
        }
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

        try {
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
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $response->withStatus(400);
        }
    }

    /**
     * @param $arr
     * @return array
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     */
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

    /**
     * @param $arr
     * @return array
     * @throws GenericYoukokException
     */
    private function mapCoursesMostPopular($arr): array
    {
        return $this->courseMapper->map(
            $arr,
            [
                CourseMapper::DATASTORE_DOWNLOAD_ESTIMATE
            ]
        );
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
