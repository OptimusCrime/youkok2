<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;
use Youkok\Enums\MostPopularElement;
use Youkok\Helpers\SessionHandler;

class PopularCoursesProcessor extends AbstractPopularListingProcessor
{
    public static function fromDelta($delta = MostPopularElement::MONTH, $limit, $cache)
    {
        return [];
        //return ElementController::getMostPopularCoursesFromDelta($limit, $delta);
    }

    public static function fromSessionHandler(SessionHandler $sessionHandler)
    {
        return new PopularCoursesProcessor($sessionHandler, 'most_popular_course');
    }
}
