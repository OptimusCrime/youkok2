<?php
namespace Youkok\Helpers;

use Cron\CronExpression;

class JobRunner
{
    const CRON_JOB = 0;
    const UPGRADE = 1;

    private $containers;

    private static $schedule = [
        '0 0 * * *' => [
            \Youkok\Jobs\UpdateMostPopularCourses::class,
            \Youkok\Jobs\UpdateMostPopularElements::class,
            \Youkok\Jobs\RemoveOldSessions::class,
        ],
    ];

    private static $upgradeSchedule = [
        \Youkok\Jobs\UpdateMostPopularCourses::class,
        \Youkok\Jobs\UpdateMostPopularElements::class,
        \Youkok\Jobs\RemoveOldSessions::class,
    ];

    public function __construct($containers)
    {
        $this->containers = $containers;
    }

    public function run($mode)
    {
        if ($mode == static::CRON_JOB and getenv('DEV') === '0') {
            $this->validateSchedule(static::$schedule, $mode);
            return null;
        }

        $this->runJobs(static::$upgradeSchedule);
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
            } catch (\Exception $e) {
                var_dump($e->getMessage());
                die();
            }
        }
    }
}
