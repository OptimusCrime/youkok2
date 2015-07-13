<?php
/*
 * File: installer.php
 * Holds: Runs the entire installing process from the command line
 * Created: 13.07.2015
 * Project: Youkok2
 */

// Get base directory
$base_dir = dirname(__FILE__) . '/../../';

// Include the boostrap file (hax)
require_once $base_dir . 'vendor/autoload.php';

// New instance of CLImate
$climate = new \League\CLImate\CLImate();

// Check if local.php is already in place
if (file_exists($base_dir . 'local.php')) {
    $input = $climate->confirm('File local.php is already in place, are you sure you want to continue?');

    // Check if the user killed the installation or not
    if (!$input->confirmed()) {
        $climate->out('Killing installation process...');
        die();
    }
}

// Get database information
while (true) {
    $input = $climate->input('Please prodivde database DNS');
    $input->defaultTo('mysql:host=localhost');
    $response = $input->prompt();
    break;
}