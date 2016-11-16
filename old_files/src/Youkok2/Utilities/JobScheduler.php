<?php
namespace Youkok2\Utilities;

use Youkok2\Utilities\Database;
use Youkok2\Youkok2;

class JobScheduler extends Youkok2
{
    
    private static $force;
    private static $jobs = [
        '0 0 * * *' => [
            'CourseDownloadUpdater',
        ],
    ];
    
    public static function init($force = false) {
        self::$force = $force;
        
        if (self::databaseConnect()) {
            $job_queue = self::getJobs();
            
            if (count($job_queue) > 0) {
                self::runJobs($job_queue);
            }
            
            Database::close();
        }
        else {
            echo 'Could not connect to database';
        }
        
        return;
    }
    
    private static function getJobs() {
        $job_queue = [];
        
        foreach (self::$jobs as $k => $v) {
            $cron = \Cron\CronExpression::factory($k);

            if ($cron->isDue() or self::$force) {
                // Expression is due, loop all the jobs
                foreach ($v as $iv) {
                    $job_queue[] = $iv;
                }
            }
        }
        
        return $job_queue;
    }
    
    private static function runJobs($jobs) {
        foreach ($jobs as $v) {
            $job = '\Youkok2\Jobs\\' . $v;
            
            $job_instance = new $job();
            $job_instance->run();
            $job_instance->done();
        }
    }
    
    private static function databaseConnect() {
        try {
            Database::connect();
            return true;
        }
        catch (Exception $e) {
            return false;
        }
    }
}
