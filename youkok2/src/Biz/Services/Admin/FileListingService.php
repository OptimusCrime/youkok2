<?php

namespace Youkok\Biz\Services\Admin;

use Youkok\Biz\Services\Models\Admin\AdminCourseService;

class FileListingService
{
    private $adminCourseService;
    private $adminFilesService;

    public function __construct(
        AdminCourseService $adminCourseService,
        FilesService $adminFilesService
    ) {
        $this->adminCourseService = $adminCourseService;
        $this->adminFilesService = $adminFilesService;
    }

    public function getAll(): array
    {
        return $this->adminFilesService->buildTree(
            $this->adminCourseService->getAllNonEmptyCourses()
        );
    }

    public function get(int $id): array
    {
        return $this->adminFilesService->buildTree(
            $this->adminCourseService->getCourse($id)
        );
    }

    public function getPending(): array
    {
        return $this->adminFilesService->buildTree(
            $this->adminCourseService->getAllCoursesWithPendingContent()
        );
    }
}
