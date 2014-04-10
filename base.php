<?php
/*
 * File: rest.php
 * Holds: The base-class intilize most of the common stuff the system needs
 * Created: 02.10.13
 * Last updated: 10.04.14
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
    protected $archive_paths = array(); // Array that holds all paths already cached by the url-reverser
    protected $file_directory = ''; // Holds the filedirectory
    protected $base_path = ''; // Holds the directory for the index file (defined as base for the project)
    protected $collection;
    private $paths; // Holds the paths served from the Loader-class
    
    //
    // Constructor
    //

    public function __construct($paths, $base) {
        // Starting session
        session_start();
        
        // Stores the base path
        $this->base_path = $base;

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
            // Init user
            $this->user = new User();
            
            // Init Smarty
            $this->template = $smarty = new Smarty();
            
            // Setting the file-directory
            $this->file_directory = dirname(__FILE__ ). '/05d26028b91686045907144f1883fcb1';
            
            // Storing paths
            $this->paths = $paths;
        }
    }
    
    //
    // Generates an url based on the current id
    //
    
    protected function generateUrlById($id, $type = 'download', $trailing_slash = false) {
        // Todo, add caching
        $url = array();
        $current_id = $id;
        
        // Loop untill we get to root
        while ($current_id != 0) {
            // Todo add caching here!
            $get_revese_url = "SELECT parent, url_friendly
            FROM archive 
            WHERE id = :id";
            
            $get_revese_url_query = $this->db->prepare($get_revese_url);
            $get_revese_url_query->execute(array(':id' => $current_id));
            $row = $get_revese_url_query->fetch(PDO::FETCH_ASSOC);
            
            // Updating the current id
            $current_id = $row['parent'];
            
            // Add to the url-array
            $url[] = $row['url_friendly'];
        }
        
        // Reverse array
        $url = array_reverse($url);
        
        // Return the complete url
        return $this->paths[$type][0] . implode('/', $url) . (($trailing_slash) ? '/' : '');
    }
    
    //
    // Returning the parent for a current url
    //
    
    protected function reverseParent($url, $is_directory) {
        // Clear out the parts of the url we don't ned
        $url_pieces = $this->cleanRequestUrl($url);
        
        // Check if root
        if (count($url_pieces) == 0) {
            return 1;
        }
        else {
            // Fetch from the database
            $current_id = 1;
            
            foreach ($url_pieces as $path) {
                // Todo add caching here!
                $get_revese_url = "SELECT id
                FROM archive 
                WHERE parent = :parent
                AND url_friendly = :url_friendly";
                
                $get_revese_url_query = $this->db->prepare($get_revese_url);
                $get_revese_url_query->execute(array(':parent' => $current_id, ':url_friendly' => $path));
                $row = $get_revese_url_query->fetch(PDO::FETCH_ASSOC);
                
                // Updating the current id
                $current_id = $row['id'];
            }
            
            // Returning the current id
            return $current_id;
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
    // Return
    //
    
    private function cleanRequestUrl($url) {
        $url_pieces_temp = array();
        if (strpos($url, '/') !== false) {
            // Multiple levels
            $url_pieces_temp = explode('/', $url);
        } else {
            // Single level
            $url_pieces_temp[] = $url;
        }
        
        // Make array with archive and download-paths
        $remove_fragments = array_merge((array) $this->paths['download'], (array) $this->paths['archive']);
        
        // Make the corret tree
        $url_pieces = array();
        foreach ($url_pieces_temp as $v) {
            if (strlen($v) > 0 and !in_array('/' . $v, $remove_fragments)) {
                $url_pieces[] = $v;
            }
        }
        
        // Return the array
        return $url_pieces;
    }
    
    //
    // Close the database-connection and clean up before displaying the template
    //
    
    protected function close() {
        $this->db = null;
    }
}
?>