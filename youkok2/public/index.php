<?php
if (!(include __DIR__ . '/../vendor/autoload.php')) {
    error_log('Dependencies are not installed!', \E_USER_ERROR);
    header("HTTP/1.1 500 Internal Server Error");
    die();
}

use Youkok\Common\App;
use Youkok\Helpers\SettingsParser;

try {
    $app = new App(SettingsParser::getSlimConfig());
    $app->run();
}
catch (\Exception $ex) {
    if (getenv('DEV') === '1') {
        echo 'Uncaught out exception!';
        var_dump($ex);
        die();
    }

    echo file_get_contents(getenv('TEMPLATE_DIRECTORY') . 'errors/500.html');
}

