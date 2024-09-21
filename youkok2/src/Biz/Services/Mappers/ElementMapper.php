<?php
namespace Youkok\Biz\Services\Mappers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Monolog\Logger;

use RedisException;
use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Download\DownloadCountService;
use Youkok\Biz\Services\UrlService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;

class ElementMapper
{
    const string PARENT_DIRECT = 'PARENT_DIRECT';
    const string PARENT_COURSE = 'PARENT_COURSE';
    const string POSTED_TIME = 'POSTED_TIME';
    const string DOWNLOADS = 'DOWNLOADS';
    const string ICON = 'ICON';
    const string DATASTORE_DOWNLOADS = 'KEEP_DOWNLOADS';

    const string DOWNLOADED_TIME = 'KEEP_DOWNLOADED_TIME';

    private UrlService $urlService;
    private CourseMapper $courseMapper;
    private DownloadCountService $downloadCountService;
    private ElementService $elementService;
    private Logger $logger;

    public function __construct(
        UrlService $urlService,
        CourseMapper $courseMapper,
        DownloadCountService $downloadCountService,
        ElementService $elementService,
        Logger $logger
    ) {
        $this->urlService = $urlService;
        $this->courseMapper = $courseMapper;
        $this->downloadCountService = $downloadCountService;
        $this->elementService = $elementService;
        $this->logger = $logger;
    }

    /**
     * @throws ElementNotFoundException
     * @throws RedisException
     */
    public function map(RouteParserInterface $routeParser, Collection $elements, array $additionalFields = []): array
    {
        $out = [];
        foreach ($elements as $element) {
            $mappedElement = $this->mapElement($routeParser, $element, $additionalFields);
            if ($mappedElement !== null) {
                $out[] = $mappedElement;
            }
        }

        return $out;
    }

    /**
     * @throws ElementNotFoundException
     * @throws RedisException
     */
    public function mapFromArray(RouteParserInterface $routeParser, array $elements, array $additionalFields = []): array
    {
        $out = [];
        foreach ($elements as $element) {
            $mappedElement = $this->mapElement($routeParser, $element, $additionalFields);
            if ($mappedElement !== null) {
                $out[] = $mappedElement;
            }
        }

        return $out;
    }

    /**
     * @throws RedisException
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function mapElement(RouteParserInterface $routeParser, Element $element, array $additionalFields = []): ?array
    {
        $arr = [
            'id' => $element->id,
            'name' => $element->name,
            'type' => $element->getType(),
            'url' => $this->urlService->urlForElement($routeParser, $element),
            'link' => $element->link,
        ];

        if (in_array(static::POSTED_TIME, $additionalFields)) {
            $arr['added'] = $element->added;
        }

        if (in_array(static::PARENT_DIRECT, $additionalFields)) {
            try {
                $parent = $this->elementService->getVisibleParentForElement($element);

                $arr['parent'] = $parent->isCourse()
                               ? $this->courseMapper->mapCourse($routeParser, $parent)
                               : $this->mapElement($routeParser, $parent);
            } catch (ElementNotFoundException $e) {
                $this->logger->warning('Failed to find parent for element: ' . $element->id);
                return null;
            }
        }

        if (in_array(static::PARENT_COURSE, $additionalFields)) {
            try {
                $course = $element->getCourse();

                if ($course === null) {
                    throw new ElementNotFoundException('Could not find coure for element ' . $element->id);
                }

                $arr['course'] = $this->courseMapper->mapCourse($routeParser, $course);
            } catch (Exception $e) {
                $this->logger->warning('Failed to find course for element: ' . $element->id);
                return null;
            }
        }

        if (in_array(static::DOWNLOADS, $additionalFields)) {
            $arr['downloads'] = $this->downloadCountService->getDownloadsForElement($element);
        }

        // This is stored in the Elements datastore (prefixed with an underscore)
        if (in_array(static::DATASTORE_DOWNLOADS, $additionalFields)) {
            $arr['downloads'] = $element->getDownloads();
        }

        if (in_array(static::ICON, $additionalFields)) {
            $arr['icon'] = $element->getIcon();
        }

        if (in_array(static::DOWNLOADED_TIME, $additionalFields)) {
            $arr['downloaded_time'] = $element->getDownloadedTime();
        }

        return $arr;
    }

    /**
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function mapBreadcrumbs(RouteParserInterface $routeParser, array $elements): array
    {
        $out = [];
        foreach ($elements as $key => $element) {
            if ($key === 0) {
                $out[] = $this->courseMapper->mapCourse($routeParser, $element);
                continue;
            }

            $out[] = $this->mapElement($routeParser, $element);
        }

        return $out;
    }

    public function mapHistory(Collection $elements): array
    {
        $out = [];
        foreach ($elements as $element) {
            if ($element->isLink()) {
                $out[] = $element->name . ' ble postet.';
                continue;
            }

            if ($element->isFile()) {
                $out[] = $element->name . ' ble lastet opp.';
                continue;
            }

            // Default text
            $out[] = $element->name . ' ble opprettet.';
        }

        return $out;
    }
}
