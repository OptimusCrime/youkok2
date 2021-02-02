<?php
if (php_sapi_name() !== 'cli') {
    error_log('Script must be called from cli', \E_USER_ERROR);
    die('Script must be called from cli');
}

if (!(include __DIR__ . '/../vendor/autoload.php')) {
    error_log('Dependencies are not installed!', \E_USER_ERROR);
    die('Dependencies are not installed!');
}

use Youkok\Common\App;
use Youkok\Helpers\SettingsParser;

$settingsParser = new SettingsParser();

try {
    $app = new App($settingsParser->getSlimConfig());
    $app->runCronJobs();
} catch (\Exception $ex) {
    echo 'Uncaught out exception!' . PHP_EOL;
    echo $ex->getMessage() . PHP_EOL;
    echo $ex->getTraceAsString() . PHP_EOL;
}
