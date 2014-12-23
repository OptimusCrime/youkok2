<?php
/*
 * File: route.php
 * Holds: Using PHPs built in server solution
 * Created: 14.12.14
 * Project: Youkok2
 */

// Check if we should strip get params
$req = $_SERVER['REQUEST_URI'];
if (strpos($req, '?') !== false) {
    $req = explode('?', $req)[0];
}

// Do the actual matching
if (preg_match('/\.(?:png|jpg|jpeg|gif|txt|css|js|woff|tff|)$/', $req)) {
    // Static request, return as is
    return false;
}
else {
    // Dynamic request
    include dirname(dirname(__FILE__)) . '/index.php';
}