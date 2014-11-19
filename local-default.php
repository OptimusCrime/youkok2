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
if (!defined('DATABASE_PASSWORD')) {
    define('VERSION', '1.0.0-dev');
}
if (!defined('DATABASE_PASSWORD')) {
    define('DEV', false);
}

// Paths
if (!defined('DATABASE_PASSWORD')) {
    define('BASE_PATH', dirname(__FILE__));
}
if (!defined('DATABASE_PASSWORD')) {
    define('FILE_PATH', BASE_PATH . '/path-to-file-root');
}
if (!defined('DATABASE_PASSWORD')) {
    define('CACHE_PATH', BASE_PATH . '/cache');
}
if (!defined('DATABASE_PASSWORD')) {
    define('TEST_PATH', BASE_PATH . '/tests');
}

// Database
if (!defined('DATABASE_PASSWORD')) {
    define('DATABASE_CONNECTION', 'mysql:host=localhost;dbname=youkok2');
}
if (!defined('DATABASE_PASSWORD')) {
    define('DATABASE_USER', 'root');
}
if (!defined('DATABASE_PASSWORD')) {
    define('DATABASE_PASSWORD', 'password');
}

// Errors
if (!defined('DATABASE_PASSWORD')) {
    define('ERROR_MODE', E_ERROR);
}
if (!defined('DATABASE_PASSWORD')) {
    define('ERROR_DISPLAY', 0);
}

// Timezone
if (!defined('DATABASE_PASSWORD')) {
    define('TIMEZONE', 'Europe/London');
}

// Define domain
if (!defined('DATABASE_PASSWORD')) {
    define('DOMAIN', 'my-site.tld');
}

// Site url (no trailing slash)
if (!defined('DATABASE_PASSWORD')) {
    define('URL', 'http://my-site.tld');
}

// Full site url (trailing slash)
if (!defined('DATABASE_PASSWORD')) {
    define('URL_FULL', 'http://my-site.tld');
}

// Relative position (trailing slash both before and after, or just / if root)
if (!defined('DATABASE_PASSWORD')) {
    define('URL_RELATIVE', '/');
}

// Accepted filetypes
if (!defined('DATABASE_PASSWORD')) {
    define('ACCEPTED_FILETYPES', 'application/pdf,text/plain,text/x-java-source,application/x-python,text/html');
}
if (!defined('DATABASE_PASSWORD')) {
    define('ACCEPTED_FILEENDINGS', 'pdf,txt,java,py,html,htm,sql,zip');
}

// Google analytics
if (!defined('DATABASE_PASSWORD')) {
    define('USE_GA', false);
}

// Email
if (!defined('DATABASE_PASSWORD')) {
    define('EMAIL_CONTACT', 'post@my-site.tld');
}