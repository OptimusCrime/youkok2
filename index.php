<?php
/*
 * File: index.php
 * Holds: The Loader class that loads the correct class based on the method being called, setting
 * output and including all the stuff we need
 * Created: 02.10.13
 * Project: Youkok2
 */

header('Content-Type: text/html; charset=utf-8');

/*
 * Include the settings and the autoloader from Composer
 */

require 'local.php';
require 'vendor/autoload.php';

/*
 * Create the autoloader for the application
 */

spl_autoload_register(function ($class) {
    // project-specific namespace prefix
    $prefix = 'Youkok2\\';
    
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
    $file = BASE_PATH . '/classes/' . str_replace('\\', '/', $relative_class) . '.php';
    
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

/*
 * Set debug options
 */

error_reporting(ERROR_MODE);
ini_set('display_errors', ERROR_DISPLAY);

/*
 * Set the timezone
 */

date_default_timezone_set(TIMEZONE);

/*
 * Check if we should initiate the Loader
 */

if (get_included_files()[0] == __FILE__) {
    // First element included files array, meaning this file is the file being called, run Loader
    $loader = new \Youkok2\Utilities\Loader();
}
?>