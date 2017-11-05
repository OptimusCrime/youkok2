<?php
namespace Youkok\Processors;

use Youkok\Helpers\SessionHandler;
use Youkok\Models\Element;
use Youkok\Utilities\ArrayHelper;

class ArchiveVisitProcessor extends AbstractElementFactoryProcessor
{
    const SESSION_KEY = 'latest_course_visited';
    const MAX_NUMBER_OF_VISITS_LISTED = 5;

    public static function fromElement(Element $element)
    {
        return new ArchiveVisitProcessor($element);
    }

    public function run()
    {
        static::addCourseToUserHistory($this->element->rootParent, $this->sessionHandler);
    }

    private static function addCourseToUserHistory(Element $element, SessionHandler $sessionHandler)
    {
        if ($element === null or !is_numeric($element->id) or !$element->isCourse()) {
            return null;
        }

        $courseVisits = $sessionHandler->getDataWithKey(static::SESSION_KEY);
        if (count($courseVisits) > 0 and $courseVisits[0] === $element->id) {
            return null;
        }

        $newCourseVisits = ArrayHelper::limitArray(
            ArrayHelper::prependToArray($courseVisits, $element->id),
            static::MAX_NUMBER_OF_VISITS_LISTED
        );

        $sessionHandler->setData(static::SESSION_KEY, $newCourseVisits, SessionHandler::MODE_OVERWRITE);
    }
}
