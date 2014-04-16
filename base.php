<?php
/*
 * File: rest.php
 * Holds: The base-class intilize most of the common stuff the system needs
 * Created: 02.10.13
 * Last updated: 15.04.14
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

            // Define a few constants in Smarty
            $this->template->assign('VERSION', VERSION);
            $this->template->assign('SITE_URL', SITE_URL);
            $this->template->assign('SITE_URL_FULL', SITE_URL_FULL);
            $this->template->assign('SITE_RELATIVE', SITE_RELATIVE);
            $this->template->assign('SITE_SEARCH_BASE', SITE_URL_FULL . substr($paths['archive'][0], 1) . '/');
            $this->template->assign('SITE_EMAIL_CONTACT', SITE_EMAIL_CONTACT);

            // Define the standard menu
            $this->template->assign('HEADER_MENU', 'HOME');

            // Init user
            $this->user = new User($this->db, $this->template);
            
            // Setting the file-directory
            $this->fileDirectory = dirname(__FILE__ ). FILE_ROOT;
            
            // Storing paths
            $this->paths = $paths;
        }
    }
    
    //
    // Returning an 404-page
    //
    
    protected function display404() {
        // Include 404 controller
        require_once $this->basePath . '/controllers/notfoundController.php';

        // New instance
        $controller = new NotfoundController($this->paths, $this->basePath);
    }
    
    //
    // Close the database-connection and clean up before displaying the template
    //
    
    protected function close() {
        $this->db = null;
    }

    //
    // Method for redirecting
    //

    protected function redirect($p) {
        // Close first
        $this->close();

        // Redirect
        header('Location: ' . SITE_URL_FULL . $p);
    }

    //
    // Method for displaying message
    //

    private function showMessages() {
        // Check if session was set
        if (isset($_SESSION['youkok2_message']) and count($_SESSION['youkok2_message']) > 0) {
            // Loop and store in variable
            $ret = '';
            foreach ($_SESSION['youkok2_message'] as $v) {
                $ret .= '<div class="alert alert-' . $v['type'] . '">' . $v['text'] . '</div>';
            }

            // Unset the session variable
            unset($_SESSION['youkok2_message']);

            // Assign final string
            $this->template->assign('SITE_MESSAGES', $ret);
        }
        else {
            $this->template->assign('SITE_MESSAGES', null);
        }
    }

    protected function addMessage($text, $type) {
        if (!isset($_SESSION['youkok2_message'])) {
            $_SESSION['youkok2_message'] = array();
        }
        
        $_SESSION['youkok2_message'][] = array('text' => $text, 'type' => $type);
    }

    //
    // Override default display method from Smarty
    //

    protected function displayAndCleanup($template) {
        // Close database
        $this->close();
        
        // Load message
        $this->showMessages();

        // Call Smarty
        $this->template->display($template);
    }
}
?>