<?php
namespace Youkok\Processors;

use Youkok\Controllers\ElementController;

class PopularCoursesProcessor
{
    public static function fromDelta($delta)
    {
        $courses = ElementController::getMostPopularCoursesFromDelta($delta);

        /*
         * $this->container->get('cache')->forever('frontpage_most_popular_elements', [1, 2, 3, 4]);
        $cache = $this->container->get('cache')->get('frontpage_most_popular_elements');
         */

        // Update user defaults

        return $courses;
    }

    public static function currentUser()
    {
        // TODO
        return self::fromDelta(1);
    }
}
