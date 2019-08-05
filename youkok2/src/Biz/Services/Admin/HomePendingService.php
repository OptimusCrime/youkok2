<?php
namespace Youkok\Biz\Services\Admin;

use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Services\Mappers\Admin\AdminElementMapper;
use Youkok\Biz\Services\Models\Admin\AdminCourseService;

class HomePendingService
{
    private $adminCourseService;
    private $adminFilesService;
    private $adminElementMapper;

    public function __construct(AdminCourseService $adminCourseService, AdminFilesService $adminFilesService)
    {
        $this->adminCourseService = $adminCourseService;
        $this->adminFilesService = $adminFilesService;
    }

    public function get(): array
    {
        $coursesWithPendingContent = $this->adminCourseService->getAllCoursesWithPendingContent();

        $content = [];
        foreach ($coursesWithPendingContent as $course) {
            try {
                $content[] = $this->adminFilesService->buildTreeFromId($course);
            }
            catch (GenericYoukokException $ex) {
                // Some legacy file is not added directory on parent, keep going, this is handled in the frontend
            }
        }

        return $content;
    }
}
