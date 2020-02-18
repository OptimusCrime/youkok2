<?php
namespace Youkok\Biz\Services;

use Illuminate\Database\Eloquent\Collection;
use Monolog\Logger;
use Youkok\Biz\Services\Models\CourseService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CoursesCacheConstants;

class CacheCollection {

    const REGULAR = "REGULAR";
    const ADMIN = "ADMIN";

    private $type;
    private $jsFile;
    private $templateFile;
    private $templateContent;

    public function __construct(string $type, string $jsFile, string $templateFile)
    {
        $this->type = $type;
        $this->jsFile = CoursesLookupService::getJsFileLocation($jsFile);
        $this->templateFile = CoursesLookupService::getTemplateFileLocation($templateFile);

        $this->templateContent = '<script src="'
            . CoursesLookupService::JS_DIRECTORY
            . $jsFile
            . '?hash=%s"></script>';
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getJsFile(): string
    {
        return $this->jsFile;
    }

    public function getTemplateFile(): string
    {
        return $this->templateFile;
    }

    public function getTemplateContent(): string
    {
        return $this->templateContent;
    }
}

class CoursesLookupService
{
    const JS_DIRECTORY = 'assets/data/';
    const JS_FILE_NAME = 'courses_lookup.js';
    const ADMIN_JS_FILE_NAME = 'courses_lookup_admin.js';
    const JS_TEMPLATE = 'var COURSES_LOOKUP = %s;';

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
        $files = static::getFilesInformation();
        $data = $this->courseService->getAllVisibleCourses();

        foreach ($files as $file) {
            $this->deleteCacheFile($file->getJsFile());
            $this->deleteCacheFile($file->getTemplateFile());

            $this->populateCacheFile($data, $file);
            $this->createCacheBustingTemplate($file);
            $this->changeOwnership($file);
        }
    }

    private static function getFilesInformation(): array
    {
        return [
            static::getFileInformation(
                CacheCollection::REGULAR,
                static::JS_FILE_NAME,
                CoursesCacheConstants::CACHE_BUSTING_FILE_NAME
            ),
            static::getFileInformation(
                CacheCollection::ADMIN,
                static::ADMIN_JS_FILE_NAME,
                CoursesCacheConstants::ADMIN_CACHE_BUSTING_FILE_NAME
            ),
        ];
    }

    private static function getFileInformation(string $type, string $jsFile, string $templateFile): CacheCollection
    {
        return new CacheCollection(
            $type,
            $jsFile,
            $templateFile
        );
    }

    static function getTemplateFileLocation(string $fileName): string
    {
        return getenv('CACHE_DIRECTORY') . CoursesCacheConstants::DYNAMIC_SUB_DIRECTORY . $fileName;
    }

    static function getJsFileLocation(string $fileName): string
    {
        return getenv('CACHE_DIRECTORY') . CoursesCacheConstants::DYNAMIC_SUB_DIRECTORY . $fileName;
    }

    private function deleteCacheFile(string $file): void
    {
        if (!file_exists($file) || !is_file($file)) {
            return;
        }

        if (!unlink($file)) {
            $this->logger->error('Failed to delete cache file in location: ' . $file);
        }
    }

    private function populateCacheFile(Collection $data, CacheCollection $file): void
    {
        $dataAsJson = $this->coursesToJsonData($data, $file->getType());
        $content = str_replace('%s', $dataAsJson, static::JS_TEMPLATE);

        $response = file_put_contents($file->getJsFile(), $content);
        if ($response === false) {
            $this->logger->error('Failed to populate cache file in location: ' . $file->getJsFile());
        }
    }

    private function coursesToJsonData(Collection $courses, string $type): string
    {
        $output = [];
        foreach ($courses as $course) {
            $output[] = $this->mapCourse($course, $type);
        }

        return json_encode($output);
    }

    private function mapCourse(Element $course, string $type): array
    {
        return [
            'id' => $course->id,
            'name' => $course->getCourseName(),
            'code' => $course->getCourseCode(),
            'url' => $type === CacheCollection::REGULAR
                ? $this->urlService->urlForCourse($course)
                : $this->urlService->urlForAdminFiles($course),
            'empty' => $course->empty === 1,
        ];
    }


    private function createCacheBustingTemplate(CacheCollection $file): void
    {
        $lookupChecksum = sha1_file($file->getJsFile());
        $content = str_replace('%s', $lookupChecksum, $file->getTemplateContent());

        $response = file_put_contents($file->getTemplateFile(), $content);

        if ($response === false) {
            $this->logger->error(
                'Failed to store content in cache busting template in location: ' . $file->getTemplateFile()
            );
        }
    }

    private function changeOwnership(CacheCollection $file): void
    {
        if (exec('whoami') === 'root') {
            chown($file->getTemplateFile(), 'www-data');
            chgrp($file->getTemplateFile(), 'www-data');

            chown($file->getJsFile(), 'www-data');
            chgrp($file->getJsFile(), 'www-data');
        }
    }
}
