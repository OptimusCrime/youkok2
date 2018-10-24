<?php
namespace Youkok\Biz\Services\Jobs;

use Youkok\Biz\Services\PopularListing\MostPopularCoursesService;

class UpdateMostPopularCoursesJobService implements JobServiceInterface
{
    private $mostPopularCoursesService;

    public function __construct(MostPopularCoursesService $mostPopularCoursesService)
    {
        $this->mostPopularCoursesService = $mostPopularCoursesService;
    }

    public function run()
    {
        $this->mostPopularCoursesService->refresh();
    }
}
