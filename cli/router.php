<?php
/*
 * File: route.php
 * Holds: Using PHPs built in server solution
 * Created: 14.12.2014
 * Project: Youkok2
 */

// Set correct port
if (isset($_SERVER['SERVER_PORT'])) {
    define('PORT', $_SERVER['SERVER_PORT']);
}
// Check if we should strip get params
$req = $_SERVER['REQUEST_URI'];
if (strpos($req, '?') !== false) {
    $req = explode('?', $req)[0];
}

// Do the actual matching
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|woff|tff|)$/', $req)) {
    // Static request, return as is
    return false;
}
else {
    // Dynamic request
    include dirname(dirname(__FILE__)) . '/index.php';
}