<?php
/*
 * File: JobScheduler.php
 * Holds: Runs jobs
 * Created: 29.11.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Utilities;

use \Youkok2\Utilities\Database as Database;
use \Youkok2\Youkok2 as Youkok2;

class JobScheduler extends Youkok2 {
    
    /*
     * Array with jobs
     */
    
    private static $jobs = [
        '0 0 * * *' => [
            'CourseDownloadUpdater',
        ],
    ];
    
    /*
     * Init the scheduler
     */
    
    public static function init() {
        // Create a database connection
        if (self::databaseConnect()) {
            // Find out what jobs to run
            $job_queue = self::getJobs();
            
            // Check if we have any jobs to run
            if (count($job_queue) > 0) {
                // Run jobs
                self::runJobs($job_queue);
            }
            
            // Close the database connection
            Database::close();
        }
        else {
            echo 'Could not connect to database';
        }
        
        // Kill the script here
        return;
    }
    
    /*
     * Job the jobs and filters the ones that are due
     */
    
    private static function getJobs() {
        $job_queue = [];
        
        // Loop the job list
        foreach (self::$jobs as $k => $v) {
            // Parse the expression to the factory here
            $cron = \Cron\CronExpression::factory($k);
            
            // Check if this expression is due
            if ($cron->isDue()) {
                // Expression is due, loop all the jobs
                foreach ($v as $iv) {
                    $job_queue[] = $iv;
                }
            }
        }
        
        // Return the list of jobs
        return $job_queue;
    }
    
    /*
     * Ru the jobs in the queue
     */
    
    private static function runJobs($jobs) {
        // Loop the list of jobs
        foreach ($jobs as $v) {
            // Get the full path to the class
            $job = '\Youkok2\Jobs\\' . $v;
            
            // Create new instance
            $job_instance = new $job();
            
            // Run the job
            $job_instance->run();
            
            // Run the done method
            $job_instance->done();
        }
    }
    
    /*
     * Connect to the database
     */
    
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