<?php
require __DIR__ . '/../vendor/autoload.php';

use Youkok\Helpers\JobRunner;
use Youkok\Youkok;
use Youkok\Utilities\EnvParser;
use Youkok\Helpers\SettingsParser;

EnvParser::parse('/etc/apache2/sites-enabled/envs/', ['default', 'production']);

$settingsParser = new SettingsParser();
$settingsParser->parse([
    __DIR__ . '/../config/default-settings.php',
    __DIR__ . '/../config/settings.php'
]);

$app = new Youkok($settingsParser->getSettings());
$app->runJobs(JobRunner::CRON_JOB);