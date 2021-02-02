<?php
namespace Youkok\Biz\Services\Job;

use Exception;

use Monolog\Logger as MonologLogger;
use Psr\Container\ContainerInterface;

use Youkok\Biz\Services\Job\Jobs\ClearReddisCachePartitionsService;
use Youkok\Biz\Services\Job\Jobs\JobServiceInterface;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularCoursesJobService;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularElementsJobService;

class JobService
{
    private static array $jobs = [
        UpdateMostPopularCoursesJobService::class,
        UpdateMostPopularElementsJobService::class,
        ClearReddisCachePartitionsService::class,
    ];

    private ContainerInterface $container;
    private MonologLogger $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(MonologLogger::class);
    }

    public function run(): void
    {
        foreach (static::$jobs as $job) {
            $this->runJob($job);
        }
    }

    private function runJob(string $jobClass): void
    {
        try {
            /** @var JobServiceInterface $job */
            $job = $this->container->get($jobClass);
            $job->run();
        }
        catch (Exception $ex) {
            $this->logger->error('Could not fetch job class: ' . $jobClass, $ex->getTrace());
        }
    }
}
