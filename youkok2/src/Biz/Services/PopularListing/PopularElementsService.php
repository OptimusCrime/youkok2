<?php
namespace Youkok\Biz\Services\PopularListing;

use Youkok\Common\Models\Element;

class PopularElementsService extends AbstractPopularListingProcessor
{
    public function fromDelta($delta, $limit)
    {
        $ids = $this->cacheService->getMostPopularElementsFromDelta($delta, $limit);
        if (empty($ids)) {
            return [];
        }

        return static::idListToElements($ids);
    }

    private static function idListToElements(array $ids)
    {
        $elements = [];
        foreach ($ids as $id => $downloads) {
            $element = Element::fromIdVisible($id, ['id', 'name', 'slug', 'uri', 'link', 'empty', 'parent', 'checksum']);
            $element->_downloads = (string) $downloads;

            $elements[] = $element;
        }
        return $elements;
    }
}
