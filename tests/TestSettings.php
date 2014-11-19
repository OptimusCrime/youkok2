<?php
/*
 * Override stuff
 */

// Directories
define('TEST_PATH', dirname(__FILE__));
define('BASE_PATH', dirname(TEST_PATH));
define('FILE_PATH', TEST_PATH . '/files');
define('CACHE_PATH', FILE_PATH . '/cache');
 
// Database
define('DATABASE_CONNECTION', 'mysql:host=localhost;dbname=youkok2_tests');