<?php
namespace Youkok\Biz\Services\Job\Jobs;

use Youkok\Biz\Services\PopularListing\MostPopularElementsService;

class UpdateMostPopularElementsJobService implements JobServiceInterface
{
    private $mostPopularElementsService;

    public function __construct(MostPopularElementsService $mostPopularElementsService)
    {
        $this->mostPopularElementsService = $mostPopularElementsService;
    }

    public function run()
    {
        $this->mostPopularElementsService->refresh();
    }
}
