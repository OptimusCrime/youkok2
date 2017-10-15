<?php
namespace Youkok\Jobs;

abstract class JobInterface
{
    protected $containers;

    public function __construct($containers)
    {
        $this->containers = $containers;
    }

    abstract public function run();
}