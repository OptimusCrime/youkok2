<?php
/*
 * File: rest.php
 * Holds: The base-class intilize most of the common stuff the system needs
 * Created: 02.10.13
 * Last updated: 05.12.13
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
    private $paths; // Holds the paths served from the Loader-class
    
    //
    // Constructor
    //

    public function __construct($paths) {
        // Starting session
        session_start();
        
        // Trying to connect to the database
        try {
            $this->db = new PDO('mysql:host=' . DATABASE_HOST.';dbname=' . DATABASE_TABLE, DATABASE_USER, DATABASE_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
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
        
        // Setting the file-directory
        $this->file_directory = dirname(__FILE__ ). '/05d26028b91686045907144f1883fcb1';
        
        // Storing paths
        $this->paths = $paths;
    }
    
    //
    // Makes reverse url-lookup
    //
    
    protected function reverseUrl($url, $include_full_path = true, $is_directory = false) {
        // Checking if we got got an empty url
        if (strlen($url) == 0) {
            return '';
        }
        
        // Checking if roo
        if ($url == '/') {
            return '/';
        }
        
        // Check if multiple levels deep
        $url_pieces_temp = array();
        if (strpos($url, '/') !== false) {
            // Multiple levels
            $url_pieces_temp = explode('/', $url);
        } else {
            // Single level
            $url_pieces_temp[] = $url;
        }
        
        // Check if we have just root left now
        if (count($url_pieces_temp) == 1) {
            return '/';
        }
        
        // Remove all empty pieces
        $location = '/';
        $url_pieces = array();
        for ($i = 1; $i < count($url_pieces_temp); $i++) {
            if (strlen($url_pieces_temp[$i]) > 0) {
                $url_pieces[] = array('location' => $location, 'url_friendly' => $url_pieces_temp[$i]);
                $location .= $url_pieces_temp[$i].'/';
            }
        }
        
        // Now build the correct string
        $real_path = '';
        foreach ($url_pieces as $path) {
            $get_revese_url = "SELECT path
            FROM archive 
            WHERE location = :location
            AND url_friendly = :url_friendly";
            
            $get_revese_url_query = $this->db->prepare($get_revese_url);
            $get_revese_url_query->execute(array(':location' => $path['location'], ':url_friendly' => $path['url_friendly']));
            $row = $get_revese_url_query->fetch(PDO::FETCH_ASSOC);
            
            $real_path = $row['path'];
        }
        
        // Return the path
        return (($this->file_directory) ? $include_full_path : '') . $real_path . (($is_directory) ? '/' : '');
    }
    
    //
    //
    //
    
    protected function generateUrlById($id) {
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
        return $this->paths['download'][0] . implode('/', $url);
    }
    
    //
    // Returning the parent for a current url
    //
    
    protected function reverseParent($url, $is_directory) {
        // Clear out the parts of the url we don't ned
        $url_pieces_temp = array();
        if (strpos($url, '/') !== false) {
            // Multiple levels
            $url_pieces_temp = explode('/', $url);
        } else {
            // Single level
            $url_pieces_temp[] = $url;
        }
        
        // Make the corret tree
        $url_pieces = array();
        foreach ($url_pieces_temp as $v) {
            if (strlen($v) > 0 and !in_array('/' . $v, $this->paths['archive'])) {
                $url_pieces[] = $v;
            }
        }
        
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
    // Close the database-connection and clean up before displaying the template
    //
    
    protected function close() {
        $this->db = null;
    }
}
?>