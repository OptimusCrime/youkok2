<?php
namespace Youkok\Biz\Services;

use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;

class UrlService
{
    private ElementService $elementService;

    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    public function urlForCourse(RouteParserInterface $routeParser, Element $element): string
    {
        return $routeParser->urlFor('archive', ['course' => $element->slug]);
    }

    public function urlForCourseAdmin(RouteParserInterface $routeParser, Element $element): string
    {
        return $routeParser->urlFor('admin_file', ['id' => $element->id]);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function urlForElement(RouteParserInterface $routeParser, Element $element): string
    {
        switch ($element->getType()) {
            case Element::LINK:
                return $this->urlForLink($routeParser, $element);
            case Element::FILE:
                return $this->urlForFile($routeParser, $element);
            default:
            case Element::DIRECTORY:
                return $this->urlForDirectory($routeParser, $element);
        }
    }

    /**
     * @throws ElementNotFoundException
     */
    private function urlForFile(RouteParserInterface $routeParser, Element $element): string
    {
        if ($element->uri !== null) {
            return $routeParser->urlFor('download', ['uri' => $element->uri]);
        }

        return $routeParser->urlFor('download', ['uri' => $this->elementService->getUriForElement($element)]);
    }

    private function urlForLink(RouteParserInterface $routeParser, Element $element): string
    {
        return $routeParser->urlFor('redirect', ['id' => $element->id]);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function urlForDirectory(RouteParserInterface $routeParser, Element $element): string
    {
        $uri = $this->getUriForDirectory($element);

        $uriFragments = explode('/', $uri);
        $course = $uriFragments[0];

        unset($uriFragments[0]);

        $params = implode('/', $uriFragments);

        return $routeParser->urlFor('archive', [
            'course' => $course,
            'path' => $params
        ]);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getUriForDirectory(Element $element): string
    {
        if ($element->uri !== null) {
            return $element->uri;
        }

        return $this->elementService->getUriForElement($element);
    }
}
