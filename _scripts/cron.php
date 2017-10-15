<?php
require __DIR__ . '/../vendor/autoload.php';

use \Youkok\Youkok;
use \Youkok\Helpers\SettingsParser;

$settingsParser = new SettingsParser();
$settingsParser->parse([
    __DIR__ . '/../config/default-settings.php',
    __DIR__ . '/../config/settings.php'
]);

$app = new Youkok($settingsParser->getSettings());
$app->runJobs(true);