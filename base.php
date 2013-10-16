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
    protected $archive_paths = array(); // Array that holds all paths already cached by the url-reverser
    protected $file_directory = ''; // Holds the filedirectory
    
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
        
        // Setting the file-directory
        $this->file_directory = dirname(__FILE__).'/05d26028b91686045907144f1883fcb1';
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
        if (strpos($url,'/') !== false) {
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
        return (($this->file_directory)?$include_full_path:'').$real_path.(($is_directory)?'/':'');
    }
    
    //
    // Makes reverse url-lookup
    //
    
    protected function prettifyUrl($url) {
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
        if (strpos($url,'/') !== false) {
            // Multiple levels
            $url_pieces_temp = explode('/',$url);
        } else {
            // Single level
            $url_pieces_temp[] = $url;
        }
        
        // Remove all empty pieces
        $url_pieces = array();
        for ($i = 0; $i < count($url_pieces_temp); $i++) {
            if (strlen($url_pieces_temp[$i]) > 0) {
                $url_pieces[] = '/'.$url_pieces_temp[$i];
            }
        }
        
        // Check if we have just root left now
        if (count($url_pieces) == 1 and $url_pieces[0] == '/') {
            return '/';
        }
        
        // Build correct paths
        $temp = '';
        for ($i = 0; $i < count($url_pieces); $i++) {
            $temp .= $url_pieces[$i];
            $url_pieces[$i] = $temp;
        }
        
        // Now build the correct string
        $ret = '';
        foreach ($url_pieces as $path) {
            $get_pretty_url = "SELECT url_friendly, path
            FROM archive 
            WHERE path = :path";
            
            $get_pretty_url_query = $this->db->prepare($get_pretty_url);
            $get_pretty_url_query->execute(array(':path' => $path));
            $row = $get_pretty_url_query->fetch(PDO::FETCH_ASSOC);
            
            $ret .= '/'.$row['url_friendly'];
        }
        
        // Remove first slash and return the url
        return substr($ret, 1);
    }
    
    //
    // 
    //
    
    protected function fromArchiveToDownload($url) {
        $split = explode('/', $url);
        
        if ($split[0] == 'arkiv') {
            $split[0] = 'last-ned';
        }
        
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