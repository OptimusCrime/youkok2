<?php
/*
 * File: local-example.php / local.php
 * Holds: Holds the database-information
 * Created: 02.10.13
 * Last updated: 23.04.14
 * Project: Youkok2
 * 
*/

// Version
define('VERSION', '0.0.1-b');

// Site url (no trailing slash)
define('SITE_URL', 'http://my-site.tld');

// Full site url (trailing slash)
define('SITE_URL_FULL', 'http://my-site.tld');

// Relative position (trailing slash both before and after, or just / if root)
define('SITE_RELATIVE', '/');

// Accepted filetypes
define('SITE_ACCEPTED_FILETYPES', 'application/pdf,text/plain,text/x-java-source,application/x-python,text/html');
define('SITE_ACCEPTED_FILEENDINGS', 'pdf,txt,java,py,html,htm');

// Email
define('SITE_EMAIL_CONTACT', 'post@my-site.tld');

// Folder for downloads
define('FILE_ROOT', '/relative-path-to-file-root');

// The host where the database is located
define('DATABASE_HOST', 'localhost');

// The username for connection to the database
define('DATABASE_USER', 'root');

// The password for connection to the database
define('DATABASE_PASSWORD', 'password');

// The table we are using
define('DATABASE_TABLE', 'youkok2');
?>