<?php

namespace Youkok\Biz\Services\PopularListing;

use Exception;
use Illuminate\Support\Collection;
use Monolog\Logger;

use RedisException;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\InvalidValueException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Mappers\CourseMapper;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Enums\MostPopularCourse;

class MostPopularCoursesService
{
    private CacheService $cacheService;
    private DownloadService $downloadService;
    private CourseMapper $courseMapper;
    private Logger $logger;

    public function __construct(
        CacheService    $cacheService,
        DownloadService $downloadService,
        CourseMapper    $courseMapper,
        Logger          $logger
    ) {
        $this->cacheService = $cacheService;
        $this->downloadService = $downloadService;
        $this->courseMapper = $courseMapper;
        $this->logger = $logger;
    }

    /**
     * @throws RedisException
     * @throws Exception
     * @throws InvalidValueException
     */
    public function getMostPopularCourses(RouteParserInterface $routeParser, MostPopularCourse $delta, int $limit): array
    {
        $key = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);
        $cache = $this->cacheService->get($key);
        if ($cache !== null) {
            return json_decode($cache, true);
        }


        $elements = $this->downloadService->getMostPopularCursesFromDelta($delta, $limit);
        $response = $this->mapMostPopularCourse($routeParser, $elements, $delta, $limit);
        $this->cacheService->set($key, json_encode($response));

        return $response;
    }

    /**
     * @throws Exception
     * @throws InvalidValueException
     */
    private function mapMostPopularCourse(RouteParserInterface $routeParser, Collection $elements, MostPopularCourse $delta, int $limit): array
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
                $fields[] = CourseMapper::DOWNLOADS_ALL;
                break;
            default:
                throw new InvalidValueException('Unexpected most popular course value: ' . $delta->getValue());

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
}
