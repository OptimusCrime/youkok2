<?php
/*
 * File: route.php
 * Holds: Using PHPs built in server solution
 * Created: 14.12.14
 * Project: Youkok2
 */

if (preg_match('/\.(?:png|jpg|jpeg|gif|txt|css|js)$/', $_SERVER['REQUEST_URI'])) {
    // Static request, return as is
    return false;
}
else {
    // Dynamic request
    include dirname(dirname(__FILE__)) . '/index.php';
}