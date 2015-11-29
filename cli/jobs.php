<?php
/*
 * File: jobs.php
 * Holds: Execute jobs from the command line
 * Created: 29.11.2015
 * Project: Youkok2
 *
 */

require_once dirname(__FILE__) . '/../local.php';
require_once dirname(__FILE__) . '/../local-default.php';
require_once BASE_PATH . '/index.php';

use \Youkok2\Utilities\JobScheduler as JobScheduler;

JobScheduler::init();