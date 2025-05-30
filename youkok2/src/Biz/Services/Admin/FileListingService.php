<?php
namespace Youkok\Biz\Services\Admin;

use Slim\Interfaces\RouteParserInterface;
use Youkok\Biz\Services\Models\Admin\AdminCourseService;

class FileListingService
{
    private AdminCourseService $adminCourseService;
    private FilesService $filesService;

    public function __construct(
        AdminCourseService $adminCourseService,
        FilesService $filesService
    ) {
        $this->adminCourseService = $adminCourseService;
        $this->filesService = $filesService;
    }

    public function getAll(RouteParserInterface $routeParser): array
    {
        return $this->filesService->buildTree(
            $routeParser,
            $this->adminCourseService->getAllCourses()
        );
    }

    public function getOne(RouteParserInterface $routeParser, int $id): array
    {
        return $this->filesService->buildTree(
            $routeParser,
            $this->adminCourseService->getSingleCourse($id)
        );
    }

    public function getPending(RouteParserInterface $routeParser): array
    {
        return $this->filesService->buildTree(
            $routeParser,
            $this->adminCourseService->getAllCoursesWithPendingContent()
        );
    }
}
