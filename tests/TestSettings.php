<?php
/*
 * File: TestSettings.php
 * Holds: Override settings for testing
 * Created: 07.09.2015
 * Project: Youkok2
 *
 */

// Directories
define('TEST_PATH', dirname(__FILE__));
define('BASE_PATH', dirname(TEST_PATH));
define('FILE_PATH', TEST_PATH . '/files');
define('CACHE_PATH', FILE_PATH . '/cache');
 
// Database
define('DATABASE_DNS', 'mysql:host=localhost;unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock');
define('DATABASE_NAME', 'youkok2_tests');