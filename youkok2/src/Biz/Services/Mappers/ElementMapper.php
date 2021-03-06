<?php
namespace Youkok\Biz\Services\Mappers;

use Illuminate\Database\Eloquent\Collection;
use Monolog\Logger;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Services\Download\DownloadCountService;
use Youkok\Biz\Services\UrlService;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;

class ElementMapper
{
    const PARENT_DIRECT = 'PARENT_DIRECT';
    const PARENT_COURSE = 'PARENT_COURSE';
    const POSTED_TIME = 'POSTED_TIME';
    const DOWNLOADS = 'DOWNLOADS';
    const ICON = 'ICON';
    const DATASTORE_DOWNLOADS = 'KEEP_DOWNLOADS';

    const DOWNLOADED_TIME = 'KEEP_DOWNLOADED_TIME';

    private UrlService $urlService;
    private CourseMapper $courseMapper;
    private DownloadCountService $downloadCountService;
    private ElementService $elementService;
    private CourseService $courseService;
    private Logger $logger;

    public function __construct(
        UrlService $urlService,
        CourseMapper $courseMapper,
        DownloadCountService $downloadCountService,
        ElementService $elementService,
        CourseService $courseService,
        Logger $logger
    ) {
        $this->urlService = $urlService;
        $this->courseMapper = $courseMapper;
        $this->downloadCountService = $downloadCountService;
        $this->elementService = $elementService;
        $this->courseService = $courseService;
        $this->logger = $logger;
    }

    /**
     * @param Collection $elements
     * @param array $additionalFields
     * @return array
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     */
    public function map(Collection $elements, array $additionalFields = []): array
    {
        $out = [];
        foreach ($elements as $element) {
            $mappedElement = $this->mapElement($element, $additionalFields);
            if ($mappedElement !== null) {
                $out[] = $mappedElement;
            }
        }

        return $out;
    }

    /**
     * @param array $elements
     * @param array $additionalFields
     * @return array
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     */
    public function mapFromArray(array $elements, array $additionalFields = []): array
    {
        $out = [];
        foreach ($elements as $element) {
            $mappedElement = $this->mapElement($element, $additionalFields);
            if ($mappedElement !== null) {
                $out[] = $mappedElement;
            }
        }

        return $out;
    }

    /**
     * @param Element $element
     * @param array $additionalFields
     * @return array|null
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     */
    public function mapElement(Element $element, array $additionalFields = []): ?array
    {
        $arr = [
            'id' => $element->id,
            'name' => $element->name,
            'type' => $element->getType(),
            'url' => $this->urlService->urlForElement($element),
            'link' => $element->link,
        ];

        if (in_array(static::POSTED_TIME, $additionalFields)) {
            $arr['added'] = $element->added;
        }

        if (in_array(static::PARENT_DIRECT, $additionalFields)) {
            try {
                $parent = $this->elementService->getVisibleParentForElement($element);

                $arr['parent'] = $parent->isCourse()
                               ? $this->courseMapper->mapCourse($parent)
                               : $this->mapElement($parent);
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

                $arr['course'] = $this->courseMapper->mapCourse($course);
            } catch (GenericYoukokException $e) {
                $this->logger->warning('Failed to find course for element: ' . $element->id);
                return null;
            }
        }

        if (in_array(static::DOWNLOADS, $additionalFields)) {
            $arr['downloads'] = $this->downloadCountService->getDownloadsForElement($element);
        }

        // This is stored in the Elements datastore (prefixed with an underscore)
        if (in_array(static::DATASTORE_DOWNLOADS, $additionalFields)) {
            $arr['downloads'] = (int)$element->getDownloads();
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
     * @param array $elements
     * @return array
     * @throws GenericYoukokException
     * @throws ElementNotFoundException
     */
    public function mapBreadcrumbs(array $elements): array
    {
        $out = [];
        foreach ($elements as $key => $element) {
            if ($key === 0) {
                $out[] = $this->courseMapper->mapCourse($element);
                continue;
            }

            $out[] = $this->mapElement($element);
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
