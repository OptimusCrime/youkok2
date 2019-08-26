<?php
namespace Youkok\Biz\Services;

use Illuminate\Database\Eloquent\Collection;
use Monolog\Logger;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Common\Models\Element;

class CoursesLookupService
{
    const JS_DIRECTORY = 'assets/data/';
    const JS_FILE_NAME = 'courses_lookup.js';
    const JS_TEMPLATE = 'var COURSES_LOOKUP = %s;';

    const CACHE_BUSTING_FILE_NAME = 'courses_lookup.html';
    const CACHE_BUSTING_TEMPLATE = '<script src="'
                                  . CoursesLookupService::JS_DIRECTORY
                                  . CoursesLookupService::JS_FILE_NAME
                                  . '?hash=%s"></script>';

    private $urlService;
    private $courseService;
    private $logger;

    public function __construct(UrlService $urlService, CourseService $courseService, Logger $logger)
    {
        $this->urlService = $urlService;
        $this->courseService = $courseService;
        $this->logger = $logger;
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

        if (!unlink($coursesLookupFile)) {
            $this->logger->error('Failed to delete cache file in location: ' . $coursesLookupFile);
        }
    }

    private function populateCacheFile(): void
    {
        $data = $this->coursesToJsonData($this->courseService->getAllVisibleCourses());
        $content = str_replace('%s', $data, static::JS_TEMPLATE);

        $response = file_put_contents(static::getJsFileLocation(), $content);
        if ($response === false) {
            $this->logger->error('Failed to populate cache file in location: ' . static::getJsFileLocation());
        }
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

        $response = file_put_contents(static::getTemplateFileLocation(), $content);

        if ($response === false) {
            $this->logger->error(
                'Failed to store content in cache busting template in location: ' . static::getTemplateFileLocation()
            );
        }
    }

    private static function getTemplateFileLocation(): string
    {
        return getenv('CACHE_DIRECTORY') . static::CACHE_BUSTING_FILE_NAME;
    }

    private static function getJsFileLocation(): string
    {
        return getenv('CACHE_DIRECTORY') . static::JS_FILE_NAME;
    }
}
