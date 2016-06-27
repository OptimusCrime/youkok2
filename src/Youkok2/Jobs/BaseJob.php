<?php
/*
 * File: BaseJob.php
 * Holds: Base job class
 * Created: 29.11.2015
 * Project: Youkok2
*/

namespace Youkok2\Jobs;

interface BaseJob
{

    /*
     * Job will be overridden here
     */
    
    public function run();
    
    /*
     * Ran once the job is done
     */
    
    public function done();
}
