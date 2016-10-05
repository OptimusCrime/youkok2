<?php
namespace Youkok2\Jobs;

/**
 * @codeCoverageIgnore
 */
interface BaseJob
{
    
    public function run();
    
    public function done();
}
