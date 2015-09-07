<?php
/*
 * File: cmd.php
 * Holds: Calling processors from the command line
 * Created: 05.12.2014
 * Project: Youkok2
 *
 */

require_once dirname(__FILE__) . '/../local.php';
require_once dirname(__FILE__) . '/../local-default.php';
require_once BASE_PATH . '/index.php';

use \Youkok2\Youkok2 as Youkok2;
use \League\CLImate\CLImate as CLImate;

/*
 * Run the code
 */

if (php_sapi_name() !== 'cli') {
    // We are not running from command line, kill
    die('No access');
}

// New instance of CLImate
$climate = new CLImate;

// Check that we got all arguments
if (count($_SERVER['argv']) != 3) {
    $climate->out('Missing arguments.');
    $climate->out('Format: php cli/cmd.php command checksum');
    die();
}

// Check that the checksum exists
if (!defined('CLI_CHECKSUM')) {
    $climate->out('Checksum not set');
    die();
}

// Match checksum
if ($_SERVER['argv'][2] != CLI_CHECKSUM) {
    $climate->out('Missing or invalid checksum');
    die();
}

// If we got this far it should be all good!
Youkok2::runProcessor($_SERVER['argv'][1], [
    'output' => true,
    'encode' => true,
    'close_db' => true]);