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
    $prefix = '';
    
    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = BASE_PATH . '/src/' . str_replace('\\', '/', $relative_class) . '.php';
    
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
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
    $youkok2->load(new Youkok2\Utilities\QueryParser());
    
    // Run the wrapper
    $wrapper->run();
}