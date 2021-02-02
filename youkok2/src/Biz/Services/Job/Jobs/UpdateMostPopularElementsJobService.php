<?php
namespace Youkok\Biz\Services\Job\Jobs;

use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;

class UpdateMostPopularElementsJobService implements JobServiceInterface
{
    private MostPopularElementsService $mostPopularElementsService;

    public function __construct(MostPopularElementsService $mostPopularElementsService)
    {
        $this->mostPopularElementsService = $mostPopularElementsService;
    }

    /**
     * @throws GenericYoukokException
     */
    public function run(): void
    {
        $this->mostPopularElementsService->refreshAll();
    }
}
