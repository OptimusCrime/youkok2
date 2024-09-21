<?php
namespace Youkok\Biz\Services\Admin;

use Exception;
use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Services\Download\DownloadFileInfoService;
use Youkok\Biz\Services\Models\Admin\AdminCourseService;
use Youkok\Biz\Services\Models\ElementService;
use Youkok\Common\Models\Element;
use Youkok\Common\Utilities\CourseDirectory;
use Youkok\Common\Utilities\SelectStatements;

class FileDetailsService
{
    private AdminCourseService $adminCourseService;
    private ElementService $elementService;
    private DownloadFileInfoService $downloadFileInfoService;

    public function __construct(
        AdminCourseService $adminCourseService,
        ElementService $elementService,
        DownloadFileInfoService $downloadFileInfoService
    ) {
        $this->adminCourseService = $adminCourseService;
        $this->elementService = $elementService;
        $this->downloadFileInfoService = $downloadFileInfoService;
    }

    /**
     * @throws ElementNotFoundException
     * @throws Exception
     */
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
                'requested_deletion'
            ],
            [
                ElementService::FLAG_FETCH_COURSE
            ]
        );

        return $this->map($element);
    }

    /**
     * @throws Exception
     */
    private function map(Element $element): array
    {
        $arr = $element->toArray();

        if ($element->getType() === Element::FILE) {
            $arr['file_exists'] = $this->downloadFileInfoService->fileExists($element);
        }

        $arr['course_tree'] = $this->mapCourseDirectoriesTree($element);
        $arr['type'] = $element->getType();
        $arr['title'] = $element->getType() === Element::COURSE
            ? $element->getCourseName() . ': ' . $element->getCourseName()
            : $element->name;

        return $arr;
    }

    /**
     * @throws Exception
     */
    private function mapCourseDirectoriesTree(Element $element): array
    {
        $list = $this->mapCourseDirectories(
            $this->adminCourseService->getCourseDirectoriesTree(
                $element->getType() === Element::COURSE ? $element : $element->getCourse()
            )
        );

        $output = [];

        foreach ($list as $value) {
            $output[] = $value->getOutput();
        }

        return $output;
    }

    /**
     * @throws Exception
     */
    private function mapCourseDirectories(Element $directory, int $depth = 0): array
    {
        $output = [
            new CourseDirectory(
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
                    $child,
                    $depth + 1
                )
            );
        }

        return $output;
    }
}
