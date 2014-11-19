<?php
/*
 * File: local-default.php / local.php
 * Holds: Holds the settings
 * Created: 02.10.13
 * Project: Youkok2
*/


namespace Youkok2;

/*
 * Settings
*/

// Version
if (!defined('VERSION')) {
    define('VERSION', '1.0.0-dev');
}
if (!defined('DEV')) {
    define('DEV', false);
}

// Base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__FILE__));
}

// File path
if (!defined('FILE_PATH')) {
    define('FILE_PATH', BASE_PATH . '/path-to-file-root');
}

// Cache path
if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', BASE_PATH . '/cache');
}

// Database
if (!defined('DATABASE_CONNECTION_INFO')) {
    define('DATABASE_CONNECTION_INFO', 'mysql:host=localhost;dbname=youkok2,root,password');
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

// Define domain
if (!defined('DOMAIN')) {
    define('DOMAIN', 'my-site.tld');
}

// Site url (no trailing slash)
if (!defined('URL')) {
    define('URL', 'http://my-site.tld');
}

// Full site url (trailing slash)
if (!defined('URL_FULL')) {
    define('URL_FULL', 'http://my-site.tld');
}

// Relative position (trailing slash both before and after, or just / if root)
if (!defined('URL_RELATIVE')) {
    define('URL_RELATIVE', '/');
}

// Accepted filetypes
if (!defined('ACCEPTED_FILETYPES')) {
    define('ACCEPTED_FILETYPES', 'application/pdf,text/plain,text/x-java-source,application/x-python,text/html');
}
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