<?php
/*
 * File: rest.php
 * Holds: The base-class intilize most of the common stuff the system needs
 * Created: 02.10.13
 * Last updated: 02.10.13
 * Project: Youkok2
 * 
*/

//
// The Base-class initializing most of the common stuff
//

class Base {

    //
    // The internal variables
    //

    protected $db; // The PDO-wrapper
    protected $user; // Hold the user-object
    protected $template; // Holds the Smarty-object
    protected $archivePaths = array(); // Array that holds all paths already cached by the url-reverser
    
    //
    // Constructor
    //

    public function __construct() {
        // Starting session
        session_start();
        
        // Trying to connect to the database
        try {
            $this->db = new PDO("mysql:host=".DATABASE_HOST.";dbname=".DATABASE_TABLE, DATABASE_USER, DATABASE_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        } catch (Exception $e) {
            $this->db = null;
        }

        // Authenticate if database-connection was successful
        if (!$this->db) {
            // Error goes here
        }
        
        // Init user
        $this->user = new User();
        
        // Init Smarty
        $this->template = $smarty = new Smarty();
    }
    
    //
    // Close the database-connection and clean up before displaying the template
    //
    
    protected function close() {
        $this->db = null;
    }
    
    
}
?>