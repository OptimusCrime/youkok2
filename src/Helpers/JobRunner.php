<?php
namespace Youkok\Helpers;

use Cron\CronExpression;

class JobRunner
{
    private $containers;

    private static $schedule = [
        '0 0 * * *' => [
            \Youkok\Jobs\UpdateMostPopularCourses::class,
            \Youkok\Jobs\UpdateMostPopularElements::class,
        ],
    ];

    public function __construct($containers)
    {
        $this->containers = $containers;
    }

    public function run($force)
    {
        $this->validateSchedule(static::$schedule, $force);
    }

    private function validateSchedule(array $schedule, $force)
    {
        foreach ($schedule as $cronExpression => $jobs) {
            $cron = CronExpression::factory($cronExpression);
            if ($cron->isDue() or $force) {
                $this->runJobs($jobs);
            }
        }
    }

    private function runJobs($jobs)
    {
        foreach ($jobs as $job) {
            try {
                $jobInstance = new $job($this->containers);
                $jobInstance->run();
            }
            catch (\Exception $e) {
                // TODO
            }
        }
    }
}