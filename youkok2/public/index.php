<?php
if (!(include __DIR__ . '/../vendor/autoload.php')) {
    error_log('Dependencies are not installed!', \E_USER_ERROR);
    header("HTTP/1.1 500 Internal Server Error");
    die();
}

use Youkok\Common\App;
use Youkok\Helpers\SettingsParser;

$app = new App(SettingsParser::getSlimConfig());
$app->run();
