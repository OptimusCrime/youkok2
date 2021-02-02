<?php
namespace Youkok\Biz\Services\Admin;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\InvalidFlagCombination;
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

    /**
     * @return array
     * @throws ElementNotFoundException
     * @throws InvalidFlagCombination
     */
    public function getAll(): array
    {
        return $this->filesService->buildTree(
            $this->adminCourseService->getAllNonEmptyCourses()
        );
    }

    /**
     * @param int $id
     * @return array
     * @throws ElementNotFoundException
     * @throws InvalidFlagCombination
     */
    public function get(int $id): array
    {
        return $this->filesService->buildTree(
            $this->adminCourseService->getCourse($id)
        );
    }

    /**
     * @return array
     * @throws ElementNotFoundException
     * @throws InvalidFlagCombination
     */
    public function getPending(): array
    {
        return $this->filesService->buildTree(
            $this->adminCourseService->getAllCoursesWithPendingContent()
        );
    }
}
