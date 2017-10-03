<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;
use Youkok\Enums\MostPopularElement;
use Youkok\Helpers\SessionHandler;

class PopularCoursesProcessor extends AbstractPopularListingProcessor
{
    public static function fromDelta($delta = MostPopularElement::MONTH)
    {
        $courses = ElementController::getMostPopularCoursesFromDelta($delta);

        /*
         * $this->container->get('cache')->forever('frontpage_most_popular_elements', [1, 2, 3, 4]);
        $cache = $this->container->get('cache')->get('frontpage_most_popular_elements');
         */

        // Update user defaults

        return $courses;
    }

    public static function fromSessionHandler(SessionHandler $sessionHandler)
    {
        return parent::fromSessionHandler($sessionHandler, 'most_popular_course');
    }
}
