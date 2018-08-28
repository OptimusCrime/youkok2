<?php
require __DIR__ . '/../vendor/autoload.php';

use Youkok\Helpers\SettingsParser;

$settingsParser = new SettingsParser();

return $settingsParser->getPhinxConfig();