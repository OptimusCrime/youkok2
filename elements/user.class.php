<?php
/*
 * File: user.php
 * Holds: Holds all the user-related stuff
 * Created: 02.10.13
 * Last updated: 02.10.13
 * Project: Youkok2
 * 
*/

Class User {

    //
    // The internal variables
    //
    
    private $id;
    private $name;
    private $is_ntnu;
    
    //
    // Constructor
    //

    public function __construct() {
        // TODO
    }
    
    //
    // Returning if the user is logged in or not
    //
    
    public function loggedIn() {
        return 1;
    }
    
    //
    // Returning the current user-id
    //
    
    public function getId() {
        return 1;
    }
    
    //
    // Returning the current username
    //
    
    public function getNick() {
        return '<i>Anonym</i>';
    }
    
    //
    // Returning if the current user is NTNU-user or not
    //
    
    public function isNTNU() {
        return 1;
    }
    
    //
    //
    //
    
    public function isVerified() {
        return 1;
    }
}
?>