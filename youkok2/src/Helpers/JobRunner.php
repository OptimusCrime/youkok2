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

    private static $jobAlises = [
        \Youkok\Jobs\UpdateMostPopularCourses::class => [
            'courses',
            'course',
            'most_popular_courses',
        ]
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
    public function runJobWithName($name)
    {
        foreach (static::$jobAlises as $job => $aliases) {
            if (in_array($name, $aliases)) {
                $this->runJob($job);
                break;
            }
        }
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
            $this->runJob($job);
        }
    }

    private function runJob($job)
    {
        try {
            $jobInstance = new $job($this->containers);
            $jobInstance->run();
        } catch (\Exception $e) {
            // TODO
        }
    }
}
