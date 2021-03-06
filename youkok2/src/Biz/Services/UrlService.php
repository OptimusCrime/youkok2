<?php
namespace Youkok\Biz\Services;

use Slim\Interfaces\RouterInterface;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;

class UrlService
{
    private RouterInterface $router;
    private ElementService $elementService;

    public function __construct(RouterInterface $router, ElementService $elementService)
    {
        $this->router = $router;
        $this->elementService = $elementService;
    }

    public function urlForCourse(Element $element): string
    {
        return $this->router->pathFor('archive', ['course' => $element->slug]);
    }

    public function urlForCourseAdmin(Element $element): string
    {
        return $this->router->pathFor('admin_file', ['id' => $element->id]);
    }

    /**
     * @param Element $element
     * @return string
     * @throws ElementNotFoundException
     */
    public function urlForElement(Element $element): string
    {
        switch ($element->getType()) {
            case Element::LINK:
                return $this->urlForLink($element);
            case Element::FILE:
                return $this->urlForFile($element);
            default:
            case Element::DIRECTORY:
                return $this->urlForDirectory($element);
        }
    }

    /**
     * @param Element $element
     * @return string
     * @throws ElementNotFoundException
     */
    private function urlForFile(Element $element): string
    {
        if ($element->uri !== null) {
            return $this->router->pathFor('download', ['uri' => $element->uri]);
        }

        return $this->router->pathFor('download', ['uri' => $this->elementService->getUriForElement($element)]);
    }

    private function urlForLink(Element $element): string
    {
        return $this->router->pathFor('redirect', ['id' => $element->id]);
    }

    /**
     * @param Element $element
     * @return string
     * @throws ElementNotFoundException
     */
    private function urlForDirectory(Element $element): string
    {
        $uri = $this->getUriForDirectory($element);

        $uriFragments = explode('/', $uri);
        $course = $uriFragments[0];

        unset($uriFragments[0]);

        $params = implode('/', $uriFragments);

        return $this->router->pathFor('archive', [
            'course' => $course,
            'path' => $params
        ]);
    }

    /**
     * @param Element $element
     * @return string
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
