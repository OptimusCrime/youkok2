<?php
namespace Youkok\Biz\Services\Job\Jobs;

use Youkok\Biz\Services\CoursesLookupService;

class PopulateCoursesLookupFileJobService implements JobServiceInterface
{
    private $coursesLookupService;

    public function __construct(CoursesLookupService $coursesLookupService)
    {
        $this->coursesLookupService = $coursesLookupService;
    }

    public function run()
    {
        $this->coursesLookupService->refresh();
    }
}
