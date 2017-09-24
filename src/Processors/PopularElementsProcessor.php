<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;

class PopularElementsProcessor
{
    public static function fromDelta($delta)
    {
        $elements = ElementController::getMostPopularElementsFromDelta($delta);

        /*
         * $this->container->get('cache')->forever('frontpage_most_popular_elements', [1, 2, 3, 4]);
        $cache = $this->container->get('cache')->get('frontpage_most_popular_elements');
         */
        // Update user defaults

        return $elements;
    }

    public static function currentUser()
    {
        // TODO
        return self::fromDelta(1);
    }
}
