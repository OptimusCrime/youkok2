<?php
namespace Youkok\Biz\Services\Element;

use Carbon\Carbon;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Cache\CacheService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CacheKeyGenerator;

class ElementService
{
    private $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getElementFromUri($uri)
    {
        $cleanUri = preg_replace("/[^A-Za-z0-9 ]/", '', $uri);

        if ($cleanUri === null || strlen($cleanUri) === 0) {
            throw new ElementNotFoundException();
        }

        $elementId = $this->cacheService->getElementFromUri($uri);

        if (is_integer($elementId)) {
            return Element::fromIdVisible($elementId);
        }

        $element = Element::fromUriFileVisible($uri);

        if ($element === null) {
            throw new ElementNotFoundException();
        }

        $this->cacheService->setByKey(CacheKeyGenerator::keyForElementUri($uri), $element->id);

        return $element;
    }

    public function getParentForElement(Element $element)
    {
        $parent = Element
            ::where('id', $element->parent)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->first();

        if (!($parent instanceof Element)) {
            throw new ElementNotFoundException();
        }

        return $parent;
    }

    public function getNumberOfVisibleFiles()
    {
        return Element
            ::where('directory', 0)
            ->where('deleted', 0)
            ->where('pending', 0)
            ->count();
    }

    public function getNumberOfFilesThisMonth()
    {
        return Element
            ::where('directory', 0)
            ->where('deleted', 0)
            ->whereDate('added', '>=', Carbon::now()->subMonth())
            ->count();
    }

    public function getLatestElements($limit = 10)
    {
        return Element::where('directory', 0)
            ->where('pending', 0)
            ->where('deleted', 0)
            ->orderBy('added', 'DESC')
            ->orderBy('name', 'DESC')
            ->limit($limit)
            ->get();
    }
}