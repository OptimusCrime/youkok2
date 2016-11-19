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
    $input = $climate->red()->confirm('File local.php is already in place, are you sure you want to continue?');

    // Check if the user killed the installation or not
    if (!$input->confirmed()) {
        $climate->out('Killing installation process...');
        die();
    }
}

// Newline
$climate->out('');

// Get database information
while (true) {
    // DNS

    $input_database_dns = $climate->input('Please prodivde database DNS:');
    $response_database_dns = $input_database_dns->prompt();

    // Database name
    $input_database_name = $climate->input('Please prodivde database name:');
    $response_database_name = $input_database_name->prompt();

    // Database name
    $input_database_user = $climate->input('Please prodivde database user:');
    $response_database_user = $input_database_user->prompt();

    // Database password
    $input_database_password = $climate->input('Please prodivde database password:');
    $response_database_password = $input_database_password->prompt();

    // Output
    $climate->out('');
    $climate->out('Testig database connection with follow config:');
    $climate->out('');
    $climate->out('DNS: ' . $response_database_dns);
    $climate->out('Database name: ' . $response_database_name);
    $climate->out('Database user: ' . $response_database_user);
    $climate->out('Database password: **********');
    $climate->out('');

    // Test the connection
    $connection = false;
    try {
        $database_test = new PDO($response_database_dns . ';dbname=' . $response_database_name, $response_database_user, $response_database_password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $connection = true;
    }
    catch(PDOException $ex){
        $connection = false;
    }

    // Check the result
    if ($connection) {
        // Successful!
        $climate->green()->out('Connection successfull.');
        break;
    }
    else {
        $climate->red()->out('Connection failed!');
        $climate->out('');
        $input = $climate->confirm('Try again?');

        // Check if we should kill the loop
        if (!$input->confirmed()) {
            break;
        }
    }
}

// Newline
$climate->out('');

// PHP built in server or Apache2
$input = $climate->confirm('Are you using the built in server?');

// Check if the user killed the installation or not
if ($input->confirmed()) {

}

// Get site information
$input_site_port = $climate->input('Enter site port:');
$response_site_port = $input_site_port->prompt();

//
$input_site_port = $climate->input('Enter site port:');
$response_site_port = $input_site_port->prompt();
