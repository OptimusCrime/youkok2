<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;
use Youkok\Enums\MostPopularElement;
use Youkok\Helpers\SessionHandler;

class PopularElementsProcessor extends AbstractPopularListingProcessor
{
    public static function fromDelta($delta = MostPopularElement::MONTH)
    {
        $elements = ElementController::getMostPopularElementsFromDelta(15, $delta);

        /*
         * $this->container->get('cache')->forever('frontpage_most_popular_elements', [1, 2, 3, 4]);
        $cache = $this->container->get('cache')->get('frontpage_most_popular_elements');
         */
        // Update user defaults

        return $elements;
    }

    public static function fromSessionHandler(SessionHandler $sessionHandler)
    {
        return parent::fromSessionHandler($sessionHandler, 'most_popular_element');
    }
}
