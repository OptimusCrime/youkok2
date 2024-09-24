<?php

namespace Youkok\Biz\Services\PopularListing;

use Exception;
use Monolog\Logger;

use RedisException;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularCourse;

class MostPopularCoursesService
{
    private CacheService $cacheService;
    private DownloadService $downloadService;
    private ElementService $elementService;
    private CourseMapper $courseMapper;
    private Logger $logger;

    public function __construct(
        CacheService    $cacheService,
        DownloadService $downloadService,
        ElementService  $elementService,
        CourseMapper    $courseMapper,
        Logger          $logger
    ) {
        $this->cacheService = $cacheService;
        $this->downloadService = $downloadService;
        $this->elementService = $elementService;
        $this->courseMapper = $courseMapper;
        $this->logger = $logger;
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    public function getMostPopularCourses(RouteParserInterface $routeParser, MostPopularCourse $delta, int $limit): array
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);
        $cache = $this->cacheService->get($key);
        if ($cache !== null) {
            return json_decode($cache, true);
        }

        $mostPopularSet = $this->cacheService->getMostPopularCoursesSetFromDelta($delta);
        if (count($mostPopularSet) === 0) {
            $this->buildMostPopularCacheSet($delta);
            $mostPopularSet = $this->cacheService->getMostPopularCoursesSetFromDelta($delta);
        }

        $elements = [];
        foreach ($mostPopularSet as $id => $downloads) {
            try {
                $element = $this->elementService->getElement(
                    new SelectStatements('id', $id),
                    [
                        ElementService::FLAG_ENSURE_VISIBLE
                    ]
                );

                $elements[] = $element;

                if (count($elements) === $limit) {
                    break;
                }
            } catch (ElementNotFoundException $ex) {
                $this->logger->debug('Could not find element ' . $id);
                continue;
            }
        }

        $response = $this->mapMostPopularCourse($routeParser, $elements, $delta, $limit);
        $this->cacheService->set($key, json_encode($response));

        return $response;
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    private function mapMostPopularCourse(RouteParserInterface $routeParser, array $elements, MostPopularCourse $delta, int $limit): array
    {
        $fields = [
        ];

        switch ($delta->getValue()) {
            case MostPopularCourse::DAY()->getValue():
                $fields[] = CourseMapper::DOWNLOADS_TODAY;
                break;
            case MostPopularCourse::WEEK()->getValue():
                $fields[] = CourseMapper::DOWNLOADS_WEEK;
                break;
            case MostPopularCourse::MONTH()->getValue():
                $fields[] = CourseMapper::DOWNLOADS_MONTH;
                break;
            case MostPopularCourse::YEAR()->getValue():
                $fields[] = CourseMapper::DOWNLOADS_YEAR;
                break;
            case MostPopularCourse::ALL()->getValue():
            default:
                $fields[] = CourseMapper::DOWNLOADS_ALL;
                break;

        }

        $response = [];
        foreach ($elements as $element) {
            try {
                $element = $this->courseMapper->mapCourse(
                    $routeParser,
                    $element,
                    $fields
                );

                $response[] = $element;

                if (count($response) === $limit) {
                    break;
                }
            } catch (ElementNotFoundException $ex) {
                $this->logger->debug($ex);
                continue;
            }
        }

        return $response;
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    private function buildMostPopularCacheSet(MostPopularCourse $delta): void
    {
        $elements = $this->downloadService->getMostPopularCurseFromDelta($delta);
        $key = CacheKeyGenerator::keyForMostPopularCoursesSetForDelta($delta);

        foreach ($elements as $element) {
            $downloads = static::getDownloadsFromElement($element, $delta);
            $this->cacheService->insertIntoSet($key, (int)$downloads, (string)$element->id);
        }
    }

    private static function getDownloadsFromElement(Element $element, MostPopularCourse $delta): int
    {
        switch ($delta->getValue()) {
            case MostPopularCourse::DAY()->getValue():
                return $element->downloads_today;
            case MostPopularCourse::WEEK()->getValue():
                return $element->downloads_week;
            case MostPopularCourse::MONTH()->getValue():
                return $element->downloads_month;
            case MostPopularCourse::YEAR()->getValue():
                return $element->downloads_year;
            case MostPopularCourse::ALL()->getValue():
            default:
                return $element->downloads_all;
        }
    }
}
