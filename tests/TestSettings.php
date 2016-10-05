<?php
// Domain and port
define('PORT', '');
define('DOMAIN', 'localhost');
define('URL_RELATIVE', '/');

// Directories
define('TEST_PATH', dirname(__FILE__));
define('BASE_PATH', dirname(TEST_PATH));
define('FILE_PATH', TEST_PATH . '/files');
define('CACHE_PATH', FILE_PATH . '/cache');
 
// Database
define('DATABASE_ADAPTER', 'sqlite');
define('DATABASE_DNS', 'sqlite::memory:');
