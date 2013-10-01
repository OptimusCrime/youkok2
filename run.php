<?php
/*
 * File: run.php
 * Holds: Method to initiate the different controllers dynamically
 * Created: 02.10.13
 * Last updated: 02.10.13
 * Project: Youkok2
 * 
*/

// Splitting the file-name, removing the tile-extention
$name = explode('.',$path[count($path)-1]);

// Uppercasing the first letter to be nice and OOP-ish
$classToCall = ucfirst($name[0]);

// Creating a new instance
$controller = new $classToCall();
?>