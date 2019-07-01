<?php
namespace Youkok\Biz\Services;

use Illuminate\Database\Eloquent\Collection;
use Youkok\Common\Controllers\CourseController;
use Youkok\Common\Models\Element;

class CoursesLookupService
{
    const JS_DIRECTORY = 'assets/data/';
    const JS_FILE_NAME = 'courses_lookup.js';
    const JS_TEMPLATE = 'var COURSES_LOOKUP = %s;';

    const CACHE_BUSTING_PATH = 'dynamic/';
    const CACHE_BUSTING_FILE_NAME = 'courses_lookup.html';
    const CACHE_BUSTING_TEMPLATE = '<script src="'
                                  . CoursesLookupService::JS_DIRECTORY
                                  . CoursesLookupService::JS_FILE_NAME
                                  . '?hash=%s"></script>';

    private $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    public function refresh(): void
    {
        $this->deleteCacheFile();
        $this->populateCacheFile();
        $this->createCacheBustingTemplate();
    }

    private function deleteCacheFile(): void
    {
        $coursesLookupFile = static::getJsFileLocation();
        if (!file_exists($coursesLookupFile) || !is_file($coursesLookupFile)) {
            return;
        }

        // TODO error handling
        unlink($coursesLookupFile);
    }

    private function populateCacheFile(): void
    {
        $data = $this->coursesToJsonData(CourseController::getAllVisibleCourses());
        $content = str_replace('%s', $data, static::JS_TEMPLATE);

        // TODO error logging?
        file_put_contents(static::getJsFileLocation(), $content);
    }

    private function coursesToJsonData(Collection $courses): string
    {
        $output = [];
        foreach ($courses as $course) {
            $output[] = $this->mapCourse($course);
        }

        return json_encode($output);
    }

    private function mapCourse(Element $course): array
    {
        return [
            'id' => $course->id,
            'name' => $course->getCourseName(),
            'code' => $course->getCourseCode(),
            'url' => $this->urlService->urlForCourse($course),
            'empty' => $course->empty === 1,
        ];
    }

    private function createCacheBustingTemplate(): void
    {
        $lookupChecksum = sha1_file(static::getJsFileLocation());
        $content = str_replace('%s', $lookupChecksum, static::CACHE_BUSTING_TEMPLATE);

        // TODO error logging?
        file_put_contents(static::getTemplateFileLocation(), $content);
    }

    private static function getTemplateFileLocation(): string
    {
        return getenv('TEMPLATE_DIRECTORY') . static::CACHE_BUSTING_PATH . static::CACHE_BUSTING_FILE_NAME;
    }

    private static function getJsFileLocation(): string
    {
        return getenv('PUBLIC_DIRECTORY') . static::JS_DIRECTORY . static::JS_FILE_NAME;
    }
}
