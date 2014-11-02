<?php
/*
 * File: local-example.php / local.php
 * Holds: Holds the settings
 * Created: 02.10.13
 * Project: Youkok2
*/


namespace Youkok2;

/*
 * Settings
*/

// Version
define('VERSION', '1.0.0-dev');
define('DEV', false);

// Base path
define('BASE_PATH', dirname(__FILE__));

// Errors
define('ERROR_MODE', E_ALL); 
define('ERROR_DISPLAY', 1); 

// Timezone
define('TIMEZONE', 'Europe/London');

// Define domain
define('DOMAIN', 'my-site.tld');

// Site url (no trailing slash)
define('URL', 'http://my-site.tld');

// Full site url (trailing slash)
define('URL_FULL', 'http://my-site.tld');

// Relative position (trailing slash both before and after, or just / if root)
define('URL_RELATIVE', '/');

// Accepted filetypes
define('ACCEPTED_FILETYPES', 'application/pdf,text/plain,text/x-java-source,application/x-python,text/html');
define('ACCEPTED_FILEENDINGS', 'pdf,txt,java,py,html,htm,sql,zip');

// Google analytics
define('USE_GA', false);

// Email
define('EMAIL_CONTACT', 'post@my-site.tld');

// Folder for downloads
define('FILE_ROOT', dirname(__FILE__ ) . '/path-to-file-root');

// The host where the database is located
define('DATABASE_HOST', 'localhost');

// The username for connection to the database
define('DATABASE_USER', 'root');

// The password for connection to the database
define('DATABASE_PASSWORD', 'password');

// The table we are using
define('DATABASE_TABLE', 'youkok2');
?>
