<?php
namespace Youkok\Biz\Services\PopularListing;

use Youkok\Common\Models\Element;

class PopularCoursesService extends AbstractPopularListingProcessor
{
    const CACHE_DIRECTORY_KEY = 'cache_directory';
    const CACHE_DIRECTORY_SUB = 'courses';

    public function fromDelta($delta, $limit = null)
    {
        $result = $this->cacheService->getMostPopularCoursesFromDelta($delta);
        if (empty($result)) {
            return [];
        }

        if ($result === null or strlen($result) === 0) {
            return [];
        }

        $resultArr = json_decode($result, true);
        if (!is_array($resultArr) or empty($resultArr)) {
            return [];
        }

        return static::resultArrayToElements($resultArr, $limit);
    }

    private static function resultArrayToElements(array $result, $limit = null)
    {
        $elements = [];
        foreach ($result as $res) {
            $element = Element::fromIdVisible($res['id'], ['id', 'name', 'slug', 'uri', 'link', 'empty', 'parent']);
            $element->_downloads = $res['downloads'];

            $elements[] = $element;
        }

        if ($limit === null) {
            return $elements;
        }

        return static::resultArrayToMaxLimit($elements, $limit);
    }

    private static function resultArrayToMaxLimit(array $elements, $limit)
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
}
