<?php
namespace Youkok\Biz\Services\PopularListing;

use Exception;
use RedisException;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\InvalidValueException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularCourse;

class MostPopularCoursesService implements MostPopularInterface
{
    const int MAX_COURSES_TO_FETCH = 10;

    private CacheService $cacheService;
    private DownloadService $downloadService;
    private ElementService $elementService;

    public function __construct(
        CacheService $cacheService,
        DownloadService $downloadService,
        ElementService $elementService
    ) {
        $this->cacheService = $cacheService;
        $this->downloadService = $downloadService;
        $this->elementService = $elementService;
    }

    /**
     * @throws RedisException
     * @throws ElementNotFoundException
     */
    public function fromDelta(string $delta, int $limit): array
    {
        try {
            return $this->resultArrayToElements(
                static::decodeJsonPayload(
                    $this->cacheService->getMostPopularCoursesFromDelta(
                        $delta
                    ),
                ),
                $limit
            );
        }
        catch (InvalidValueException $ex) {
            return $this->resultArrayToElements(
                $this->refreshForDelta(
                    $delta
                ),
                $limit
            );
        }

    }

    /**
     * @throws RedisException
     */
    public function refreshAll(): void
    {
        foreach (MostPopularCourse::collection() as $delta) {
            $this->refresh($delta);
        }
    }

    /**
     * @throws RedisException
     */
    public function refresh(string $delta): string
    {
        $cacheKey = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);
        $this->cacheService->delete($cacheKey);

        return json_encode($this->refreshForDelta($delta));
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    private function refreshForDelta(string $delta): array
    {
        $courses = $this->downloadService->getMostPopularCoursesFromDelta($delta, static::MAX_COURSES_TO_FETCH);
        $setKey = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);

        $this->cacheService->set($setKey, json_encode($courses));

        return $courses;
    }

    /**
     * @throws ElementNotFoundException
     */
    private function resultArrayToElements(array $result, int $limit): array
    {
        $elements = [];
        foreach ($result as $res) {
            $element = $this->elementService->getElement(
                new SelectStatements('id', $res['id']),
                ['id', 'name', 'slug', 'uri', 'link', 'empty', 'parent'],
                [
                    ElementService::FLAG_ENSURE_VISIBLE
                ]
            );
            $element->setDownloads($res['downloads']);

            $elements[] = $element;
        }

        return static::resultArrayToMaxLimit($elements, $limit);
    }

    private static function resultArrayToMaxLimit(array $elements, int $limit): array
    {
        $newElements = [];
        foreach ($elements as $element) {
            $newElements[] = $element;
            if (count($newElements) === $limit) {
                break;
            }
        }

        return $newElements;
    }

    /**
     * @throws InvalidValueException
     */
    private static function decodeJsonPayload(?string $payload): array
    {
        if ($payload === null or mb_strlen($payload) === 0) {
            throw new InvalidValueException('Payload is empty');
        }

        $resultArr = json_decode($payload, true);
        if (!is_array($resultArr) or empty($resultArr)) {
            throw new InvalidValueException('Payload is invalid JSON');
        }

        return $resultArr;
    }
}
