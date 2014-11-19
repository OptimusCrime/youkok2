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
@define('VERSION', '1.0.0-dev');
@define('DEV', false);

// Base path
@define('BASE_PATH', dirname(__FILE__));

// File path
@define('FILE_PATH', BASE_PATH . '/path-to-file-root');

// Cache path
@define('CACHE_PATH', BASE_PATH . '/cache');

// Database
@define('DATABASE_CONNECTION', 'mysql:host=localhost;dbname=youkok2');
@define('DATABASE_USER', 'root');
@define('DATABASE_PASSWORD', 'password');

// Errors
@define('ERROR_MODE', E_ERROR);
@define('ERROR_DISPLAY', 0);

// Timezone
@define('TIMEZONE', 'Europe/London');

// Define domain
@define('DOMAIN', 'my-site.tld');

// Site url (no trailing slash)
@define('URL', 'http://my-site.tld');

// Full site url (trailing slash)
@define('URL_FULL', 'http://my-site.tld');

// Relative position (trailing slash both before and after, or just / if root)
@define('URL_RELATIVE', '/');

// Accepted filetypes
@define('ACCEPTED_FILETYPES', 'application/pdf,text/plain,text/x-java-source,application/x-python,text/html');
@define('ACCEPTED_FILEENDINGS', 'pdf,txt,java,py,html,htm,sql,zip');

// Google analytics
@define('USE_GA', false);

// Email
@define('EMAIL_CONTACT', 'post@my-site.tld');