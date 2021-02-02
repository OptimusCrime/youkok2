<?php
namespace Youkok\Biz\Services\PopularListing;

use Monolog\Logger;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidFlagCombination;
use Youkok\Biz\Exceptions\InvalidValueException;
use Youkok\Biz\Services\CacheService;
use Youkok\Biz\Services\Models\DownloadService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Utilities\CacheKeyGenerator;
use Youkok\Common\Utilities\SelectStatements;
use Youkok\Enums\MostPopularCourse;

class MostPopularCoursesService implements MostPopularInterface
{
    const MAX_COURSES_TO_FETCH = 10;

    private CacheService $cacheService;
    private Logger $logger;
    private DownloadService $downloadService;
    private ElementService $elementService;

    public function __construct(
        CacheService $cacheService,
        Logger $logger,
        DownloadService $downloadService,
        ElementService $elementService
    ) {
        $this->cacheService = $cacheService;
        $this->logger = $logger;
        $this->downloadService = $downloadService;
        $this->elementService = $elementService;
    }

    /**
     * @param string $delta
     * @param int $limit
     * @return array
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
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
        } catch (InvalidValueException $ex) {
            return $this->resultArrayToElements(
                $this->refreshForDelta(
                    $delta
                ),
                $limit
            );
        }

    }

    /**
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    public function refreshAll(): void
    {
        foreach (MostPopularCourse::collection() as $delta) {
            $this->refresh($delta);
        }
    }

    /**
     * @param string $delta
     * @return string
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    public function refresh(string $delta): string
    {
        $cacheKey = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);
        $this->cacheService->delete($cacheKey);

        return json_encode($this->refreshForDelta($delta));
    }

    /**
     * @param string $delta
     * @return array
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    private function refreshForDelta(string $delta): array
    {
        $courses = $this->downloadService->getMostPopularCoursesFromDelta($delta, static::MAX_COURSES_TO_FETCH);
        $setKey = CacheKeyGenerator::keyForMostPopularCoursesForDelta($delta);

        $this->cacheService->set($setKey, json_encode($courses));

        return $courses;
    }

    /**
     * @param array $result
     * @param int $limit
     * @return array
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
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

        if ($limit === null) {
            return $elements;
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
     * @param string|null $payload
     * @return array
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
