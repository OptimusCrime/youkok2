<?php
/*
 * File: Harness.php
 * Holds: Some stuff to make Yuokok2 testready
 * Created: 19.11.2014
 * Project: Youkok2
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

/*
 * Create directories
*/

if (!is_dir(FILE_PATH)) {
    mkdir(FILE_PATH);
}
if (!is_dir(CACHE_PATH)) {
    mkdir(CACHE_PATH);
}

/*
 * Connect to database
 */

// TODO

/*
 * Populate database
 */

// TODO