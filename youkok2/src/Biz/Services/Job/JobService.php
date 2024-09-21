<?php
namespace Youkok\Biz\Services\Job;

use Exception;

use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use Psr\Container\NotFoundExceptionInterface;
use Youkok\Biz\Services\Job\Jobs\ClearRedisCachePartitionsService;
use Youkok\Biz\Services\Job\Jobs\JobServiceInterface;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularCoursesJobService;
use Youkok\Biz\Services\Job\Jobs\UpdateMostPopularElementsJobService;

class JobService
{
    private static array $jobs = [
        UpdateMostPopularCoursesJobService::class,
        UpdateMostPopularElementsJobService::class,
        ClearRedisCachePartitionsService::class,
    ];

    private ContainerInterface $container;
    private Logger $logger;

    public function __construct(ContainerInterface $container, Logger $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(): void
    {
        foreach (static::$jobs as $job) {
            $this->runJob($job);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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
