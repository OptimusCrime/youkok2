<?php

namespace Youkok\Biz\Services\Admin;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Youkok\Biz\Services\Download\DownloadFileInfoService;
use Youkok\Biz\Services\Models\Admin\AdminCourseService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CourseDirectory;
use Youkok\Common\Utilities\SelectStatements;

class FileDetailsService
{
    private $adminCourseService;
    private $adminFilesService;
    private $elementService;
    private $downloadFileInfoService;

    public function __construct(
        AdminCourseService $adminCourseService,
        AdminFilesService $adminFilesService,
        ElementService $elementService,
        DownloadFileInfoService $downloadFileInfoService
    )
    {
        $this->adminCourseService = $adminCourseService;
        $this->adminFilesService = $adminFilesService;
        $this->elementService = $elementService;
        $this->downloadFileInfoService = $downloadFileInfoService;
    }

    public function get(int $id): array
    {
        $element = $this->elementService->getElement(
            new SelectStatements('id', $id),
            [
                'id',
                'name',
                'slug',
                'uri',
                'parent',
                'empty',
                'checksum',
                'size',
                'directory',
                'pending',
                'deleted',
                'link',
                'added',
                'last_visited',
            ], [
                ElementService::FLAG_FETCH_COURSE
            ]
        );

        return $this->map($element);
    }

    private function map(Element $element): array
    {
        $arr = $element->toArray();

        if ($element->getType() === Element::FILE) {
            $arr['file_exists'] = $this->downloadFileInfoService->fileExists($element);
        }

        $arr['course_tree'] = $this->mapCourseDirectoriesTree($element);

        return $arr;
    }

    private function mapCourseDirectoriesTree(Element $element): array
    {
        $list = $this->mapCourseDirectories(
            $element,
            $this->adminCourseService->getCourseDirectoriesTree($element->getCourse())
        );

        $output = [];

        /** @var CourseDirectory $value */
        foreach ($list as $value) {
            $output[] = $value->getOutput();
        }

        return $output;
    }

    private function mapCourseDirectories(Element $currentElement, Element $directory, int $depth = 0): array
    {
        $output = [
            new CourseDirectory(
                $currentElement,
                $directory,
                $depth
            )
        ];

        if (count($directory->getChildren()) === 0) {
            return $output;
        }

        foreach ($directory->getChildren() as $child) {
            $output = array_merge(
                $output,
                $this->mapCourseDirectories(
                    $currentElement,
                    $child,
                    $depth + 1
                )
            );
        }

        return $output;
    }
}
