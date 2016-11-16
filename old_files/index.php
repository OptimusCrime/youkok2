<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once dirname(__FILE__) . '/local.php';
require_once dirname(__FILE__) . '/local-default.php';
require_once BASE_PATH . '/vendor/autoload.php';

spl_autoload_register(function ($class) {
    $prefix = 'Youkok2\\';
    
    // List of the directories we should autoload from
    $directories = [
        [
            'path' => BASE_PATH . '/src/',
            'strip' => false
        ], [
            'path' => BASE_PATH . '/tests/',
            'strip' => true
        ]
    ];
    
    foreach ($directories as $v) {
        $relative_class = $class;

        // Check if we should strip the prefix
        if ($v['strip']) {
            // Does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // no, move to the next registered autoloader
                return;
            }

            // Get the relative class name
            $relative_class = substr($class, $len);
        }

        $file = $v['path'] . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

\PHP_Timer::start();

error_reporting(ERROR_MODE);
ini_set('display_errors', ERROR_DISPLAY);
date_default_timezone_set(TIMEZONE);

$youkok2 = new Youkok2\Youkok2();
$wrapper = new Youkok2\Wrapper\Wrapper($youkok2);

$call_loader = false;
if (get_included_files()[0] == __FILE__) {
    // First element included files array, meaning this file is the file being called, run Loader
    $call_loader = true;
}
else {
    // Check if running the built in server
    $boot = explode('/', get_included_files()[0]);
    if ($boot[count($boot) - 1] == 'router.php') {
        $call_loader = true;
    }
}

if ($call_loader) {
    $youkok2->setInformation();
    $youkok2->load(new Youkok2\Utilities\QueryParser($youkok2));
    $wrapper->run();
}
