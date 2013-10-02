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
    // Makes reverse url-lookup
    //
    
    protected function reverseUrl($url) {
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
            $get_revese_url = "SELECT name, path
            FROM archive 
            WHERE path = :path";
            
            $get_revese_url_query = $this->db->prepare($get_revese_url);
            $get_revese_url_query->execute(array(':path' => $path));
            $row = $get_revese_url_query->fetch(PDO::FETCH_ASSOC);
            
            $ret .= '/'.$row['name'];
        }
        
        // Turn url-friendly and return
        return $this->frienlyUrl(substr($ret,1));
    }
    
    //
    // Build url-friendly-url
    //
    
    protected function frienlyUrl($url) {
        // Turn all lowercase
        $url = strtolower($url);
        
        // Replace space with -
        $url = str_replace(' ','-',$url);
        
        // Return all
        return $url;
    }
    
    //
    // Close the database-connection and clean up before displaying the template
    //
    
    protected function close() {
        $this->db = null;
    }
    
    
}
?>