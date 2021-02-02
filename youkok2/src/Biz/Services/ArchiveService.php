<?php
namespace Youkok\Biz\Services;

use Illuminate\Database\Eloquent\Collection;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidFlagCombination;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;

class ArchiveService
{
    private ElementMapper $elementMapper;
    private ElementService $elementService;
    private CourseService $courseService;

    public function __construct(
        ElementMapper $elementMapper,
        ElementService $elementService,
        CourseService $courseService
    ) {
        $this->elementMapper = $elementMapper;
        $this->elementService = $elementService;
        $this->courseService = $courseService;
    }

    /**
     * @param int $id
     * @return Collection
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    public function get(int $id): Collection
    {
        $directory = $this->elementService->getElement(
            new SelectStatements('id', $id),
            ['id', 'name', 'slug', 'uri', 'parent', 'directory'],
            [
                ElementService::FLAG_ENSURE_VISIBLE,
                ElementService::FLAG_FETCH_PARENTS,
                ElementService::FLAG_FETCH_COURSE,
                ElementService::FLAG_FETCH_URI,
            ]
        );

        return $this->elementService->getVisibleChildren($directory);
    }

    /**
     * @param string $uri
     * @return Element
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    public function getArchiveElementFromUri(string $uri): Element
    {
        return $this->elementService->getElementFromUri(
            $uri,
            ['id', 'parent', 'name', 'slug', 'uri', 'empty', 'checksum', 'link', 'directory', 'requested_deletion'],
            [
                ElementService::FLAG_ENSURE_VISIBLE,
                ElementService::FLAG_ONLY_DIRECTORIES,
                ElementService::FLAG_FETCH_PARENTS
            ]
        );
    }

    /**
     * @param Element $element
     * @return array
     * @throws GenericYoukokException
     * @throws ElementNotFoundException
     */
    public function getBreadcrumbsForElement(Element $element): array
    {
        // The list of parents does not include the current element, add it
        $breadcrumbs = $element->getParents();
        $breadcrumbs[] = $element;

        return $this->elementMapper->mapBreadcrumbs($breadcrumbs);
    }

    /**
     * @param Element $element
     * @return string
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     */
    public function getSiteTitle(Element $element): string
    {
        if ($element->isCourse()) {
            return 'Bidrag for ' . $element->getCourseCode() . ' - ' . $element->getCourseName();
        }

        $course = $element->getCourse();

        if ($course === null) {
            throw new ElementNotFoundException('No course loaded for element ' . $element->id);
        }

        return 'Bidrag i '
            . $element->name
            . ' for '
            . $course->getCourseCode()
            . ' - '
            . $course->getCourseName();
    }

    /**
     * @param Element $element
     * @return string
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     */
    public function getSiteDescription(Element $element): string
    {
        if ($element->isCourse()) {
            return 'Bidrag for '
                . $element->getCourseCode()
                . ' - '
                . $element->getCourseName()
                . ' fra Youkok2, den beste kokeboka på nettet.';
        }

        $course = $element->getCourse();

        if ($course === null) {
            throw new ElementNotFoundException('No course loaded for element ' . $element->id);
        }

        return $this->getSiteTitle($course);
    }
}
