<?php
/*
 * File: rest.php
 * Holds: The base-class intilize most of the common stuff the system needs
 * Created: 02.10.13
 * Last updated: 12.04.14
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
    protected $fileDirectory; // Holds the filedirectory
    protected $basePath; // Holds the directory for the index file (defined as base for the project)
    protected $collection;
    protected $paths; // Holds the paths served from the Loader-class
    
    //
    // Constructor
    //

    public function __construct($paths, $base) {
        // Starting session, if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Stores the base path
        $this->basePath = $base;
        
        // Init the collection
        $this->collection = new Collection();
        
        // Trying to connect to the database
        try {
            $this->db = new PDO('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_TABLE, DATABASE_USER, DATABASE_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        } catch (Exception $e) {
            $this->db = null;
        }
        
        // Authenticate if database-connection was successful
        if ($this->db) {
            // Add root element to collection
            $root_element = new Item($this->collection, $this->db);
            $root_element->createById(1);
            $this->collection->add($root_element);

            // Init Smarty
            $this->template = $smarty = new Smarty();

            // Init user
            $this->user = new User($this->db, $this->template);
            
            // Setting the file-directory
            $this->fileDirectory = dirname(__FILE__ ). '/05d26028b91686045907144f1883fcb1';
            
            // Storing paths
            $this->paths = $paths;
        }
    }
    
    //
    // Returning an 404-page
    //
    
    protected function display404() {
        // Inclyde 404 controller
        require_once $this->basePath . '/controllers/notfoundController.php';

        $controller = new NotfoundController($this->paths, $this->basePath);
    }
    
    //
    // Close the database-connection and clean up before displaying the template
    //
    
    protected function close() {
        $this->db = null;
    }
}
?>