<?php
namespace Youkok\Biz\Services;

use Psr\Container\ContainerInterface;
use Youkok\Biz\Services\Jobs\RemoveOldSessionsJobServiceService;
use Youkok\Biz\Services\Jobs\UpdateMostPopularCoursesJobService;
use Youkok\Biz\Services\Jobs\UpdateMostPopularElementsJobService;

class JobService
{
    const CRON_JOB = 0;
    const UPGRADE = 1;

    private static $schedule = [
        RemoveOldSessionsJobServiceService::class,
        UpdateMostPopularCoursesJobService::class,
        UpdateMostPopularElementsJobService::class,
    ];

    private static $upgrade = [
        RemoveOldSessionsJobServiceService::class,
        UpdateMostPopularCoursesJobService::class,
        UpdateMostPopularElementsJobService::class,
    ];

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function run($mode)
    {
        $this->runJobs($mode === JobService::CRON_JOB ? static::$schedule : static::$upgrade);
    }

    private function runJobs($jobs)
    {
        foreach ($jobs as $job) {
            $this->runJob($job);
        }
    }

    private function runJob($jobClass)
    {
        try {
            $job = $this->container->get($jobClass);
            $job->run();
        } catch (\Exception $e) {
            // TODO
        }
    }
}
