<?php
namespace Youkok\Biz\Services;

use Slim\Interfaces\RouterInterface;
use Youkok\Common\Models\Element;

class UrlService
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function urlForCourse(Element $element): string
    {
        return $this->router->pathFor('archive', ['course' => $element->slug]);
    }

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

    private function urlForFile(Element $element): string
    {
        return $this->router->pathFor('download', ['uri' => $this->getFullUri($element)]);
    }

    private function urlForLink(Element $element): string
    {
        return $this->router->pathFor('redirect', ['id' => $element->id]);
    }

    private function urlForDirectory(Element $element): string
    {
        $uri = $this->getFullUri($element);

        $uriFragments = explode('/', $uri);
        $course = $uriFragments[0];

        unset($uriFragments[0]);

        $params = implode('/', $uriFragments);

        return $this->router->pathFor('archive', [
            'course' => $course,
            'path' => $params
        ]);
    }

    private function getFullUri(Element $element): string
    {
        if ($element->uri !== null and strlen($element->uri) > 0) {
            return $element->uri;
        }

        $parents = $element->getParentsVisible(['id', 'name', 'slug', 'uri', 'parent']);
        $uriFragments = [];

        foreach ($parents as $parent) {
            $uriFragments[] = $parent->slug;
        }

        return implode('/', $uriFragments);
    }
}
