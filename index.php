<?php
/*
 * File: index.php
 * Holds: Define stuff, create autoloader function
 * Created: 02.10.2013
 * Project: Youkok2
 * 
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * Include the settings and the autoloader from Composer
 */

include_once dirname(__FILE__) . '/local.php';
require_once dirname(__FILE__) . '/local-default.php';
require_once BASE_PATH . '/vendor/autoload.php';

/*
 * Create the autoloader for the application
 */

spl_autoload_register(function ($class) {
    // project-specific namespace prefix
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

    // Loop the directories and see if we find the file we are trying to load in one of them
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

        // Concatinate the path and the class name + namespace
        $file = $v['path'] . str_replace('\\', '/', $relative_class) . '.php';

        // Check if the file exists
        if (file_exists($file)) {
            // Require the file
            require $file;

            // Quit the function
            return;
        }
    }
});

/*
 * Start timing
 */

\PHP_Timer::start();

/*
 * Set various things
 */

error_reporting(ERROR_MODE);
ini_set('display_errors', ERROR_DISPLAY);
date_default_timezone_set(TIMEZONE);

/*
 * Create new instance of Youkok2
 */

$youkok2 = new Youkok2\Youkok2();
$wrapper = new Youkok2\Wrapper\Wrapper($youkok2);

/*
 * Check if we should run the wrapper
 */

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
    // Load a view uding the QueryParser to parse the URL
    $youkok2->setInformation();
    $youkok2->load(new Youkok2\Utilities\QueryParser($youkok2));
    
    // Run the wrapper
    $wrapper->run();
}
