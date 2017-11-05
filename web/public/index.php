<?php
if (PHP_SAPI === 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    return !is_file($file);
}

require __DIR__ . '/../vendor/autoload.php';

use Youkok\Youkok;
use Youkok\Helpers\SettingsParser;


$settingsParser = new SettingsParser();
$settingsParser->parse([
    __DIR__ . '/../config/default-settings.php',
    __DIR__ . '/../config/settings.php'
]);

$app = new Youkok($settingsParser->getSettings());
$app->run();
