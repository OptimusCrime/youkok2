<?php
namespace Youkok\Biz\Services;

use Youkok\Common\Controllers\CourseController;
use Youkok\Common\Models\Element;

class AutocompleteService
{
    const AUTOCOMPLETE_DIRECTORY = 'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'data';
    const AUTOCOMPLETE_FILE = 'autocomplete.js';
    const AUTOCOMPLETE_FILE_TEMPLATE = 'var AUTOCOMPLETE_DATA = %s;';

    private $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    public function refresh()
    {
        //$this->deleteCacheFile();
        $this->populateCacheFile();
    }

    public function getContent()
    {
        $file = static::getAutocompleteFileLocation();
        if (!file_exists($file) || !is_readable($file)) {
            return null;
        }

        return file_get_contents($file);
    }

    private function deleteCacheFile()
    {
        $autocompleteFile = static::getAutocompleteFileLocation();
        if (!file_exists($autocompleteFile) || !is_file($autocompleteFile)) {
            return true;
        }

        return unlink($autocompleteFile);
    }

    private function populateCacheFile()
    {
        $data = $this->coursesToJsonData(CourseController::getAllVisibleCourses());

        $content = str_replace('%s', $data, static::AUTOCOMPLETE_FILE_TEMPLATE);

        return file_put_contents(static::getAutocompleteFileLocation(), $content);
    }

    private function coursesToJsonData($courses)
    {
        $output = [];
        foreach ($courses as $course) {
            $output[] = $this->mapCourse($course);
        }

        return json_encode($output);
    }

    private function mapCourse(Element $course)
    {
        return [
            'id' => $course->id,
            'name' => $course->getCourseName(),
            'code' => $course->getCourseCode(),
            'url' => $this->urlService->urlForCourse($course)
        ];
    }

    private static function getAutocompleteFileLocation()
    {
        return getenv('BASE_DIRECTORY') . static::AUTOCOMPLETE_DIRECTORY . DIRECTORY_SEPARATOR . static::AUTOCOMPLETE_FILE;
    }
}
