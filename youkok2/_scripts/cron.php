<?php
if (php_sapi_name() !== 'cli') {
    die('Script must be called from cli');
}

if (!(include __DIR__ . '/../vendor/autoload.php')) {
    error_log('Dependencies are not installed!', \E_USER_ERROR);
    header("HTTP/1.1 500 Internal Server Error");
    die();
}

use Youkok\Common\App;
use Youkok\Biz\Services\Job\JobService;
use Youkok\Helpers\SettingsParser;

$settingsParser = new SettingsParser();

$app = new App($settingsParser->getSlimConfig());

if (count($argv) > 1) {
    $app->runJobs(JobService::SPECIFIC_JOB, $argv[1]);
}
else {
    $app->runJobs(JobService::CRON_JOB);
}
