<?php
namespace Youkok\Biz\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;

use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Mappers\ElementMapper;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\SelectStatements;

class ArchiveService
{
    private ElementMapper $elementMapper;
    private ElementService $elementService;

    public function __construct(
        ElementMapper $elementMapper,
        ElementService $elementService,
    ) {
        $this->elementMapper = $elementMapper;
        $this->elementService = $elementService;
    }

    /**
     * @throws ElementNotFoundException
     */
    public function get(int $id): Collection
    {
        $directory = $this->elementService->getElement(
            new SelectStatements('id', $id),
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
     * @throws ElementNotFoundException
     */
    public function getArchiveElementFromUri(string $uri): Element
    {
        return $this->elementService->getElementFromUri(
            $uri,
            [
                ElementService::FLAG_ENSURE_VISIBLE,
                ElementService::FLAG_ONLY_DIRECTORIES,
                ElementService::FLAG_FETCH_PARENTS
            ]
        );
    }

    /**
     * @throws ElementNotFoundException
     */
    public function getBreadcrumbsForElement(RouteParserInterface $routeParser, Element $element): array
    {
        // The list of parents does not include the current element, add it
        $breadcrumbs = $element->getParents();
        $breadcrumbs[] = $element;

        return $this->elementMapper->mapBreadcrumbs($routeParser, $breadcrumbs);
    }

    /**
     * @throws ElementNotFoundException
     * @throws Exception
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
     * @throws ElementNotFoundException
     * @throws Exception
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
