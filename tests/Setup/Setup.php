<?php
/*
 * File: Harness.php
 * Holds: Some stuff to make Yuokok2 testready
 * Created: 19.11.2014
 * Project: Youkok2
*/

namespace Youkok2\Setup;

use \Youkok2\Utilities\Database as Database;

/*
 * Get current location
 */

$file_location = dirname(__FILE__);

/*
 * Define some constants
 */

// Database
define('DATABASE_CONNECTION', 'sqlite:' . $file_location . '/files/test.db');
define('DATABASE_USER', null);
define('DATABASE_PASSWORD', null);

// Directories
define('FILE_PATH', $file_location . '/files/files/');
define('CACHE_PATH', $file_location . '/files/cache/');

/*
 * Include the bootstrap file
 */

require dirname(__FILE__) . '/../index.php';