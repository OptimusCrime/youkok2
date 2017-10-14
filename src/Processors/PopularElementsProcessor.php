<?php
namespace Youkok\Processors;

use Youkok\Enums\MostPopularElement;
use Youkok\Helpers\CacheHelper;
use Youkok\Helpers\SessionHandler;
use Youkok\Models\Element;

class PopularElementsProcessor extends AbstractPopularListingProcessor
{
    public static function fromDelta($delta = MostPopularElement::MONTH, $limit, $cache)
    {
        $ids = CacheHelper::getMostPopularElementsFromDelta($cache, $delta, $limit);
        if (empty($ids)) {
            return [];
        }

        return static::idListToElements($ids);
    }

    public static function fromSessionHandler(SessionHandler $sessionHandler)
    {
        return new PopularElementsProcessor($sessionHandler, 'most_popular_element');
    }

    private static function idListToElements(array $ids)
    {
        $elements = [];
        foreach ($ids as $id => $downloads) {
            $element = Element::fromId($id, ['id', 'name', 'slug', 'uri', 'link', 'empty', 'parent']);
            $element->_downloads = (string) $downloads;

            $elements[] = $element;
        }
        return $elements;
    }
}
