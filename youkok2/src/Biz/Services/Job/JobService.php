<?php
namespace Youkok\Biz\Services\Job;

use Psr\Container\ContainerInterface;
use Youkok\Biz\Services\Job\Jobs\PopulateAutocompleteFileJobService;
use Youkok\Biz\Services\Job\Jobs\RemoveOldSessionsJobServiceJobService;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularCoursesJobService;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularElementsJobService;

class JobService
{
    const CRON_JOB = 0;
    const UPGRADE = 1;
    const SPECIFIC_JOB = 2;

    private static $schedule = [
        RemoveOldSessionsJobServiceJobService::class,
        UpdateMostPopularCoursesJobService::class,
        UpdateMostPopularElementsJobService::class,
        PopulateAutocompleteFileJobService::class,
    ];

    private static $upgrade = [
        RemoveOldSessionsJobServiceJobService::class,
        UpdateMostPopularCoursesJobService::class,
        UpdateMostPopularElementsJobService::class,
        PopulateAutocompleteFileJobService::class,
    ];

    private static $codeMapping = [
        'autocomplete' => PopulateAutocompleteFileJobService::class
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

    public function runCode($code)
    {
        if (isset(static::$codeMapping[$code])) {
            $this->runJob(static::$codeMapping[$code]);
        }
        else {
            echo 'No job with code: ' . $code . '.' . PHP_EOL;
            die();
        }
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