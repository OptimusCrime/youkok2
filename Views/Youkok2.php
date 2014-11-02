<?php
/*
 * File: youkok2.class.php
 * Holds: The class that all the controllers extends
 * Created: 02.10.13
 * Project: Youkok2
*/

namespace Youkok2\Views;

use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Routes as Routes;

/*
 * Class that all the controllers extends
 */

class Youkok2 {

    /*
     * Internal variables
     */

    // Public pointers
    public $template;
    
    // Some private variables for debugging and development
    private $startTime;
    private $endTime;
    private $sqlLog;
    private $query;
    
    /*
     * Constructor
     */

    public function __construct($kill = false) {
        // Store start time
        $this->startTime = microtime(true);
        
        // Starting session, if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Trying to connect to the database
        try {
            Database::connect();
            
            // Set debug log
            if (DEV) {
                $this->sqlLog = array();
                Database::setLog($this->sqlLog);
            }
        }
        catch (Exception $e) {
            $this->db = null;
        }
        
        // Init Smarty
        $this->template = new \Smarty();
        $this->template->left_delimiter = '[[+'; 
        $this->template->right_delimiter = ']]';
        
        // Set caching
        $this->template->setCacheDir(BASE_PATH . '/cache/');
        
        // Define a few constants in Smarty
        $this->template->assign('VERSION', VERSION);
        $this->template->assign('DEV', DEV);
        $this->template->assign('SITE_URL', URL);
        $this->template->assign('SITE_TITLE', 'Den beste kokeboka på nettet');
        $this->template->assign('SITE_USE_GA', USE_GA);
        $this->template->assign('SITE_URL_FULL', URL_FULL);
        $this->template->assign('SITE_RELATIVE', URL_RELATIVE);
        $this->template->assign('SITE_SEARCH_BASE', URL_FULL . substr(Routes::getRoutes()['Archive'][0], 1) . '/');
        $this->template->assign('SITE_EMAIL_CONTACT', EMAIL_CONTACT);
        $this->template->assign('SEARCH_QUERY', '');
        $this->template->assign('HEADER_MENU', 'HOME');
        
        // Check if we should validate login
        if (isset($_POST['login-email'])) {
            Me::logIn();
        }
        
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
        // New instance
        $controller = new NotFound();
    }
    
    //
    // Close the database-connection and process queued cache
    //
    
    protected function close() {
        // Process cache
        $this->cacheManager->store();

        // Close connection
        Database::close();
    }

    //
    // Method for redirecting
    //

    public function redirect($p) {
        // Close first
        $this->close();

        // Redirect
        header('Location: ' . URL_FULL . $p);
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

            // Loop all files and make the message "pretty"
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
            $ret .= '<div class="alert alert-success">' . $file_msg . ' ble lagt til. '
                  . 'Takk for ditt bidrag!<div class="alert-close"><i class="fa fa-times"></i></div></div>';
            
            // Unset the session variable
            unset($_SESSION['youkok2_files']);
        }
        
        // Check for normal messages
        if (isset($_SESSION['youkok2_message']) and count($_SESSION['youkok2_message']) > 0) {
            foreach ($_SESSION['youkok2_message'] as $v) {
                $ret .= '<div class="alert alert-' . $v['type'] . '">' . $v['text']
                      . '<div class="alert-close"><i class="fa fa-times"></i></div></div>';
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

    public function addMessage($text, $type) {
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
            $this->template->assign('DEV_QUERIES_NUM', number_format($this->db->getCount()));
            $this->template->assign('DEV_QUERIES', $this->cleanSqlLog($this->sqlLog));
        }
        
        // Close database and process cache
        $this->close();
        
        // Load message
        $this->showMessages();
        
        // Load cache
        $this->cacheManager->loadTypeaheadCache();
        
        // Display load time
        $this->endTime = microtime(true);
        $microtime_calc = round((($this->endTime - $this->startTime)*1000), 4);
        $this->template->assign('DEV_TIME', $microtime_calc);
        
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
        
        // Check that we have some acutal queries here
        if (count($arr) > 0) {
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
}