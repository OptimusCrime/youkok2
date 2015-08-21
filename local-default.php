<?php
/*
 * File: local-default.php / local.php
 * Holds: Holds the settings
 * Created: 02.10.13
 * Project: Youkok2
*/

// Define if the site is available or not
if (!defined('AVAILABLE')) {
    define('AVAILABLE', true);
}

// Define if we're developing offline or not
if (!defined('OFFLINE')) {
    define('OFFLINE', false);
}

// Version
if (!defined('VERSION')) {
    define('VERSION', '1.0.0-dev');
}
if (!defined('DEV')) {
    define('DEV', false);
}

// Paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__FILE__));
}
if (!defined('FILE_PATH')) {
    define('FILE_PATH', BASE_PATH . '/path-to-file-root');
}
if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', BASE_PATH . '/cache');
}
if (!defined('TEST_PATH')) {
    define('TEST_PATH', BASE_PATH . '/tests');
}

// Database
if (!defined('DATABASE_DNS')) {
    define('DATABASE_DNS', 'mysql:host=localhost');
}
if (!defined('DATABASE_NAME')) {
    define('DATABASE_NAME', 'youkok2');
}
if (!defined('DATABASE_USER')) {
    define('DATABASE_USER', 'root');
}
if (!defined('DATABASE_PASSWORD')) {
    define('DATABASE_PASSWORD', 'password');
}
if (!defined('DATABASE_ERROR_MODE')) {
    define('DATABASE_ERROR_MODE', \PDO::ERRMODE_WARNING);
}

// Errors
if (!defined('ERROR_MODE')) {
    define('ERROR_MODE', E_ERROR);
}
if (!defined('ERROR_DISPLAY')) {
    define('ERROR_DISPLAY', 0);
}

// Timezone
if (!defined('TIMEZONE')) {
    define('TIMEZONE', 'Europe/London');
}

// Define port
if (!defined('PORT')) {
    // Use no port (default 80)
    define('PORT', '');
}

// Define domain
if (!defined('DOMAIN')) {
    define('DOMAIN', 'localhost');
}

// Relative position (trailing slash both before and after, or just / if root)
if (!defined('URL_RELATIVE')) {
    define('URL_RELATIVE', '/');
}

// Accepted filetypes
if (!defined('ACCEPTED_FILEENDINGS')) {
    define('ACCEPTED_FILEENDINGS', 'pdf,txt,java,py,html,htm,sql,zip');
}

// Google analytics
if (!defined('USE_GA')) {
    define('USE_GA', false);
}

// Email
if (!defined('EMAIL_CONTACT')) {
    define('EMAIL_CONTACT', 'post@my-site.tld');
}

// Display instead of download
if (!defined('DISPLAY_INSTEAD_OF_DOWNLOAD')) {
    define('DISPLAY_INSTEAD_OF_DOWNLOAD', 'text_x-python-script,text_plain,application_pdf');
}

// CSRF token
if (!defined('CSRF_KEY')) {
    define('CSRF_KEY', 'foobar');
}

// Define URLs
define('URL', 'http://' . DOMAIN . ((PORT == 80 or PORT == '') ? '' : (':' . PORT)));
define('URL_FULL', URL . URL_RELATIVE);