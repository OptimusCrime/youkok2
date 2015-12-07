<?php
/*
 * File: local-default.php / local.php
 * Holds: Holds the settings
 * Created: 02.10.2013
 * Project: Youkok2
 * 
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
    define('VERSION', '2.0.0-pl');
}
if (!defined('DEV')) {
    define('DEV', false);
}

// File paths
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

// Various production/development things
if (!defined('COMPRESS_ASSETS')) {
    define('COMPRESS_ASSETS', true);
}
if (!defined('PROFILE_QUERIES')) {
    define('PROFILE_QUERIES', false);
}
if (!defined('USE_GA')) {
    define('USE_GA', false);
}
if (!defined('CSRF_KEY')) {
    define('CSRF_KEY', 'foobar');
}
if (!defined('TIMEZONE')) {
    define('TIMEZONE', 'Europe/London');
}

// URL and port settings
if (!defined('PORT')) {
    // Use no port (default 80)
    define('PORT', '');
}
if (!defined('DOMAIN')) {
    // Just the domain
    define('DOMAIN', 'localhost');
}
if (!defined('URL_RELATIVE')) {
    // The relative URL (use / if on the root)
    define('URL_RELATIVE', '/');
}

// Email
if (!defined('EMAIL_CONTACT')) {
    define('EMAIL_CONTACT', 'post@my-site.tld');
}

// File system handler information
if (!defined('DISPLAY_INSTEAD_OF_DOWNLOAD')) {
    define('DISPLAY_INSTEAD_OF_DOWNLOAD', 'text_x-python-script,text_plain,application_pdf');
}
if (!defined('ACCEPTED_FILEENDINGS')) {
    define('ACCEPTED_FILEENDINGS', 'pdf,txt,java,py,html,htm,sql,zip');
}

// Define URLs
define('URL', 'http://' . DOMAIN . ((PORT == 80 or PORT == '') ? '' : (':' . PORT)));
define('URL_FULL', URL . URL_RELATIVE);