<?php
namespace Youkok\Biz\Services\Job\Jobs;

use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidFlagCombination;
use Youkok\Biz\Services\PopularListing\MostPopularCoursesService;

class UpdateMostPopularCoursesJobService implements JobServiceInterface
{
    private MostPopularCoursesService $mostPopularCoursesService;

    public function __construct(MostPopularCoursesService $mostPopularCoursesService)
    {
        $this->mostPopularCoursesService = $mostPopularCoursesService;
    }

    /**
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    public function run(): void
    {
        $this->mostPopularCoursesService->refreshAll();
    }
}
