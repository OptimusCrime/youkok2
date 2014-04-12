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
        // Starting session
        session_start();
        
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
    // Returns the physical location of a file based on the pretty-url
    //
    
    protected function reverseFileLocation($url) {
        // Clear out the parts of the url we don't ned
        $url_pieces = $this->cleanRequestUrl($url);
        
        // Define variables we are going to need for later
        $current_id = 1;
        $url = array();
        
        foreach ($url_pieces as $path) {
            // Todo add caching here!
            $get_revese_url = "SELECT id, location
            FROM archive 
            WHERE parent = :parent
            AND url_friendly = :url_friendly";

            $get_revese_url_query = $this->db->prepare($get_revese_url);
            $get_revese_url_query->execute(array(':parent' => $current_id, ':url_friendly' => $path));
            $row = $get_revese_url_query->fetch(PDO::FETCH_ASSOC);
            
            // Updating the current id
            $current_id = $row['id'];
            
            // Updating the current url
            $url[] = $row['location'];
        }

        // Returning the entire path
        return $this->file_directory . '/' . implode('/', $url);
    }
    
    //
    // Changed params to switch from link in archive to a download-link
    //
    
    protected function fromArchiveToDownload($url) {
        // Splitting the url
        $split = explode('/', $url);
        
        // Checking for keyword in the first part of the string
        if ('/' . $split[0] == $this->paths['archive'][0]) {
            // Replacing the string it we found the archive-path
            $split[0] = str_replace('/', '', $this->paths['download'][0]);
        }
        
        // Return the new url
        return implode('/', $split);
    }
    
    //
    // Returning an 404-page (more to come!)
    //
    
    protected function return404() {
        return '<h1>Page not found... More to come!</h1>';
    }
    
    //
    // Close the database-connection and clean up before displaying the template
    //
    
    protected function close() {
        $this->db = null;
    }
}
?>