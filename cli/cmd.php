<?php
/*
 * File: cli.php
 * Holds: Calling processors from the command line
 * Created: 05.12.2014
 * Project: Youkok2
 */

require_once dirname(__FILE__) . '/../local.php';
require_once dirname(__FILE__) . '/../local-default.php';
require_once BASE_PATH . '/index.php';

/*
 * Define what classes to use
 */

use \Youkok2\Youkok2 as Youkok2;

/*
 * Run the code
 */

if (php_sapi_name() !== 'cli') {
    // No access
    die('No access');
}
else {
    // Called from the command line
    if (count($_SERVER['argv']) != 3) {
        die('Missing arguments. Format command checksum');
    }

    // Check checksum
    if (!defined('CLI_CHECKSUM')) {
        die('Checksum not set');
    }

    if ($_SERVER['argv'][2] != CLI_CHECKSUM) {
        die('Missing or invalid checksum');
    }
    
    /*
     * Script goes here
     */
    
    Youkok2::runProcessor($_SERVER['argv'][1], ['output' => true, 'encode' => true]);
}
