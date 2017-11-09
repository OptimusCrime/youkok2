<?php
namespace Youkok\Processors\Admin;

use Youkok\Controllers\ElementController;

class ElementListPendingProcessor
{
    public static function run()
    {
        $coursesPendingMapping = static::getCoursePendingMapping();

        $courses = [];
        foreach ($coursesPendingMapping as $courseId => $pending) {
            $courses[] = [
                'COURSE' => ElementListProcessor::fetchChildrenForId($courseId),
                'PENDING' => $pending
            ];
        }

        return $courses;
    }

    private static function getCoursePendingMapping()
    {
        $pendingList = ElementController::getAllPending();
        return static::getAllCoursesFromPending($pendingList);
    }

    public static function fetchPendingForId($id)
    {
        if ($id === null) {
            return [];
        }

        $id = (int) $id;

        $coursesPendingMapping = static::getCoursePendingMapping();
        foreach ($coursesPendingMapping as $courseId => $pending) {
            if ($courseId === $id) {
                return $pending;
            }
        }

        return [];
    }

    private static function getAllCoursesFromPending($pendingList)
    {
        $courses = [];
        $coursesMapping = [];
        foreach ($pendingList as $pending) {
            $rootParent = $pending->rootParentAll->id;
            if (!in_array($rootParent, $courses)) {
                $courses[] = $rootParent;
                $coursesMapping[$rootParent] = [];
            }

            $coursesMapping[$rootParent][] = $pending;
        }

        return $coursesMapping;
    }
}
