<?php
/*
 * File: rest.php
 * Holds: The base-class intilize most of the common stuff the system needs
 * Created: 02.10.13
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
    protected $collection; // Holds the collection of items
    protected $paths; // Holds the paths served from the Loader-class
    private $norwegianMonths = array('jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'des');
    private $startTime; // Keeps track of load time
    private $endTime; // Keeps track of load time
    private $sqlLog; // Holds the sqllog
    private $query;
    
    //
    // Constructor
    //

    public function __construct($paths, $base, $kill = false) {
        // If development, start time here
        if (DEV) {
            $this->startTime = MICROTIME(TRUE);
        }
        
        // Starting session, if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Stores the base path
        $this->basePath = $base;
        
        // Trying to connect to the database
        try {
            $this->db = new PDO2('mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_TABLE, DATABASE_USER, DATABASE_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            
            // Init the collection
            $this->collection = new Collection($this->db);
            
            // Set debug log
            if (DEV) {
                $this->sqlLog = array();
                $this->db->setLog($this->sqlLog);
            }
        } catch (Exception $e) {
            $this->db = null;
        }
        
        // Init Smarty
        $this->template = $smarty = new Smarty();
        
        // Set caching
        $this->template->setCacheDir($this->basePath . '/cache/');
        
        // Define a few constants in Smarty
        $this->template->assign('VERSION', VERSION);
        $this->template->assign('DEV', DEV);
        $this->template->assign('SITE_URL', SITE_URL);
        $this->template->assign('SITE_TITLE', 'Den beste kokeboka');
        $this->template->assign('SITE_USE_GA', SITE_USE_GA);
        $this->template->assign('SITE_URL_FULL', SITE_URL_FULL);
        $this->template->assign('SITE_RELATIVE', SITE_RELATIVE);
        $this->template->assign('SITE_SEARCH_BASE', SITE_URL_FULL . substr($paths['archive'][0], 1) . '/');
        $this->template->assign('SITE_EMAIL_CONTACT', SITE_EMAIL_CONTACT);
        
        // Check if in panic mode or not
        if ($kill == false) {
            // Authenticate if database-connection was successful
            if ($this->db !== NULL) {
                // Define the standard menu
                $this->template->assign('HEADER_MENU', 'HOME');

                // Init user
                $this->user = new User($this->db, $this->template);
                
                // Setting the file-directory
                $this->fileDirectory = FILE_ROOT;
                
                // Storing paths
                $this->paths = $paths;

                // Check if we should validate login
                if (isset($_POST['login-email'])) {
                    $this->logIn();
                }
            }
            else {
                // Include 404 controller
                require_once $this->basePath . '/controllers/error.controller.php';

                // New instance
                $controller = new ErrorController($this->paths, $this->basePath, 'db');
                
                // Kill this off
                die();
            }
        }
        
        // Analyze query
        $this->queryAnalyze();
    }
    
    //
    // Methods for analyzing, reading and returning the query
    //
    
    private function queryAnalyze() {
        // Init array
        $this->query = array();

        // Split query
        if (isset($_GET['q'])) {
            $q = explode('/', $_GET['q']);

            // Read fragments
            if (count($q) > 0) {
                foreach ($q as $v) {
                    if (strlen($v) > 0) {
                        $this->query[] = $v;
                    }
                }
            }
        }
    }
    protected function queryGetSize() {
        return count($this->query);
    }
    protected function queryGet($i, $prefix = '', $endfix = '') {
        if (count($this->query) >= $i) {
            return $prefix . $this->query[$i] . $endfix;
        }
    }
    protected function queryGetAll() {
        if (isset($_GET['q'])) {
            return $_GET['q'];
        }
        else {
            return null;
        }
    }
    protected function queryGetClean($prefix = '', $endfix = '') {
        if (count($this->query) > 0) {
            return $prefix . implode('/', $this->query) . $endfix;
        }
        else {
            return null;
        }
    }
    
    //
    // Returning an 404-page
    //
    
    protected function display404() {
        // Include 404 controller
        require_once $this->basePath . '/controllers/notfound.controller.php';

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
        // Keep the string here
        $ret = '';
        
        // Check for files
        if (isset($_SESSION['youkok2_files']) and count($_SESSION['youkok2_files']) > 0) {
            $file_msg = '';
            foreach ($_SESSION['youkok2_files'] as $k => $v) {
                if (count($_SESSION['youkok2_files']) == 1) {
                    $file_msg .= $v;
                }
                else if (count($_SESSION['youkok2_files']) == 2 and $k == 1) {
                    $file_msg .= ' og ' . $v;
                }
                else {
                    if ((count($_SESSION['youkok2_files']) - 1) == $k) {
                        $file_msg .= ' og ' . $v;
                    }
                    else {
                        $file_msg .= ', ' . $v;
                    }
                }
            }
            
            // Remove the ugly part
            if (count($_SESSION['youkok2_files']) > 1) {
                $file_msg = substr($file_msg, 2);
            }
            
            // Build final string
            $ret .= '<div class="alert alert-success">' . $file_msg . ' ble lastet opp til Youkok2. \
                     Takk for ditt bidrag!<div class="alert-close"><i class="fa fa-times"></i></div></div>';
            
            // Unset the session variable
            unset($_SESSION['youkok2_files']);
        }
        
        // Check for normal messages
        if (isset($_SESSION['youkok2_message']) and count($_SESSION['youkok2_message']) > 0) {
            
            foreach ($_SESSION['youkok2_message'] as $v) {
                $ret .= '<div class="alert alert-' . $v['type'] . '">' . $v['text'] . 
                        '<div class="alert-close"><i class="fa fa-times"></i></div></div>';
            }
            
            // Unset the session variable
            unset($_SESSION['youkok2_message']);
        }
        
        // Check if any message was found
        if (strlen($ret) > 0) {
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
    
    protected function addFileMessage($name) {
        if (!isset($_SESSION['youkok2_files'])) {
            $_SESSION['youkok2_files'] = array();
        }
        
        $_SESSION['youkok2_files'][] = $name;
    }

    //
    // Override default display method from Smarty
    //

    protected function displayAndCleanup($template, $sid = null) {
        // If develop, assign dev variables
        if (DEV) {
            $this->endTime = MICROTIME(TRUE);
            $this->template->assign('DEV_TIME', $this->endTime - $this->startTime);
            $this->template->assign('DEV_QUERIES_NUM', number_format($this->db->getCount()));
            $this->template->assign('DEV_QUERIES', $this->cleanSqlLog($this->sqlLog));
        }
        
        // Close database
        $this->close();
        
        // Load message
        $this->showMessages();
        
        // Load cache
        $this->loadCache();
        
        // Call Smarty
        $this->template->display($template, $sid);
    }
    
    //
    // Cleans up the sql log
    //
    
    private function cleanSqlLog($arr) {
        // Some variables
        $str = '';
        $has_prepare = false;
        $prepare_val = array();
        
        // Loop each post
        foreach ($arr as $v) {
            // Temp variables
            $temp_loc = $temp_loc = $this->cleanSqlBacktrace($v['backtrace']);
            $temp_query = '';
            
            // Check what kind of query we're dealing with
            if (isset($v['query'])) {
                // Normal query (no binds)
                $temp_query = $v['query'];
            }
            else if (isset($v['exec'])) {
                // Normal exec (no binds)
                $temp_query = $v['exec'];
            }
            else {
                // Either bind or prepare
                if (isset($v['prepare'])) {
                    // Query is being preared
                    $has_prepare = true;
                    $prepare_val = $v['prepare'];
                }
                else if (isset($v['execute'])) {
                    // Query is executed with binds, check if binds are found
                    if ($has_prepare) {
                        // Binds are found, replace keys with bind values
                        $temp_query = str_replace(array_keys($v['execute']), $v['execute'], $prepare_val);
                        
                        // Reset prepare-value
                        $has_prepare = false;
                    }
                }
            }
            
            // Clean up n stuff
            if (!$has_prepare) {
                $str .= $temp_loc . '<pre>' . str_replace('    ', '', $temp_query) . '</pre>';
            }
        }
        
        // Return resulting string
        return $str;
    }
    private function cleanSqlBacktrace($arr) {
        if (count($arr) > 0) {
            $trace = $arr[0];
            if (count($arr) == 1) {
                return '<p>' . $trace['file'] . ' @ line ' . $trace['line'] . ':</p>';
            }
            else {
                $tooltip = '';
                $lim = ((count($arr) > 15) ? 14 : (count($arr) - 1));
                
                for ($i = 1; $i <= $lim; $i++) {
                    $trace_temp = $arr[$i];
                    $tooltip .= ($i + 1) . '. ' . $trace_temp['file'] . ' @ line ' . $trace_temp['line'] . "&#xA;";
                }
                return '<p style="cursor: help;" title="' . $tooltip . '">' . $trace['file'] . ' @ line ' . $trace['line'] . ':</p>';
            }
        }
    }
    
    //
    // Loading cache for typeahad
    //
    
    private function loadCache() {
        if (file_exists($this->basePath . '/cache.php')) {
            // File exists
            $content = json_decode(file_get_contents($this->basePath . '/cache.php'), true);
            
            // Check content
            if (!isset($content['ts'])) {
                // Assign random cache
                $this->template->assign('CACHE_TIME', rand());
            }
            else {
                // Assign corret cache
                $this->template->assign('CACHE_TIME', $content['ts']);
            }
        }
        else {
            // Assign random cache
            $this->template->assign('CACHE_TIME', rand());
        }
    }
    
    //
    // Login
    //

    protected function logIn() {
        // Check if logged in
        if (!$this->user->isLoggedIn()) {
            // Okey
            if (isset($_POST['login-email']) and isset($_POST['login-pw'])) {
                // Try to fetch email
                $get_login_user = "SELECT id, email, salt, password
                FROM user 
                WHERE email = :email";
                
                $get_login_user_query = $this->db->prepare($get_login_user);
                $get_login_user_query->execute(array(':email' => $_POST['login-email']));
                $row = $get_login_user_query->fetch(PDO::FETCH_ASSOC);

                // Check result
                if (isset($row['id'])) {
                    // Try to match password
                    $hash = $this->hashPassword($_POST['login-pw'], $row['salt']);
                    
                    // Try to match with password from the database
                    if ($hash === $row['password']) {
                        // Check remember me
                        if (isset($_POST['login-remember']) and $_POST['login-remember'] == 'pizza') {
                            $remember_me = true;
                        }
                        else {
                            $remember_me = true;
                        }

                        // Set login
                        $this->setLogin($hash, $_POST['login-email'], $remember_me);

                        // Add message
                        $this->addMessage('Du er nå logget inn.', 'success');
                        
                        // Check if we should redirect the user back to the previous page
                        if (strstr($_SERVER['HTTP_REFERER'], SITE_URL) !== false) {
                            // Has referer, remove base
                            $clean_referer = str_replace(SITE_URL_FULL, '', $_SERVER['HTTP_REFERER']);
                            
                            // Check if anything left
                            if (strlen($clean_referer) > 0) {
                                // Refirect to whatever we have left
                                $this->redirect($clean_referer);
                            }
                            else {
                                // Send to frontpage
                                $this->redirect('');
                            }
                        }
                        else {
                            // Does not have referer
                            $this->redirect('');
                        }
                    }
                    else {
                        // Message
                        $this->addMessage('Oiisann. Feil brukernavn og/eller passord. Prøv igjen.', 'danger');
                        
                        $this->redirect('logg-inn');
                    }
                }
                else {
                    // Message
                    $this->addMessage('Oiisann. Feil brukernavn og/eller passord. Prøv igjen.', 'danger');
                    
                    // Display
                    $this->redirect('logg-inn');
                }
            }
            else {
                // Not submitted or anything, just redirect
                $this->redirect('');
            }
        }
    }

    //
    // Hash password
    //

    protected function hashPassword($pass, $salt, $hard = true) {
        // Create hash
        $hash = password_hash($pass, PASSWORD_BCRYPT, array('cost' => 12, 'salt' => $salt));

        // Check if the hash should be fucked up in addition
        if ($hard) {
            return password_fuckup($hash);
        }
        else {
            return $hash;
        }
    }

    protected function setLogin($hash, $email, $cookie = false) {
        // Remove old login (just in case)
        unset($_SESSION['youkok2']);
        
        // Set the cookie
        setcookie('youkok2', null, time() - (60 * 60 * 24), '/');

        // Set new login
        $strg = $email . 'asdkashdsajheeeeehehdffhaaaewwaddaaawww' . $hash;
        if ($cookie) {
            setcookie('youkok2', $strg, time() + (60 * 60 * 24 * 31), '/');
        }
        else {
            $_SESSION['youkok2'] = $strg;
        }
    }
    
    //
    // Prettify dates
    //
    
    protected function prettifySQLDate($d) {
        $split1 = explode(' ', $d);
        $split2 = explode('-', $split1[0]);
        
        return (int) $split2[2] . '. ' . $this->norwegianMonths[$split2[1] - 1] . ' ' . $split2[0] . ' @ ' . $split1[1];
    }
}
?>