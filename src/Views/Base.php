<?php
/*
 * File: Base.php
 * Holds: Base view class
 * Created: 02.10.13
 * Project: Youkok2
*/

namespace Youkok2\Views;

/*
 * Define what classes to use
 */

use \Youkok2\Youkok2 as Youkok2;
use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\CacheManager as CacheManager;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Loader as Loader;
use \Youkok2\Utilities\MessageManager as MessageManager;
use \Youkok2\Utilities\Routes as Routes;

/*
 * Class that all the controllers extends
 */

class Base extends Youkok2 {

    /*
     * Internal variables
     */

    public $template;
    private $query;
    private $sqlLog;
    
    /*
     * Constructor
     */

    public function __construct($kill = false) {
        // Check if we're offline
        if ($kill == false and defined('OFFLINE') and OFFLINE) {
            // We're offline, check if we should be allowed still
            if (!defined('OFFLINE_WHITELIST') or (defined('OFFLINE_WHITELIST') and OFFLINE_WHITELIST != $_SERVER['REMOTE_ADDR'])) {
                // Not whitelisted, kill
                new Error('offline');
                die();
            }
        }
        
        // Trying to connect to the database
        if ($kill == false) {
            try {
                Database::connect();
                
                // Set debug log
                if (DEV) {
                    $this->sqlLog = [];
                    Database::setLog($this->sqlLog);
                }
            }
            catch (\Exception $e) {
                $this->db = null;
                
                new Error('db');
                die();
            }
        }
        
        // Init Smarty
        $this->template = new \Smarty();
        $this->template->left_delimiter = '[[+'; 
        $this->template->right_delimiter = ']]';
        
        // Set caching
        $this->template->setCacheDir(CACHE_PATH . '/smarty/');
        
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
        
        // Check if we should kill the view
        if ($kill == false) {
            // Init the user
            Me::init();
            
            // Set BASE_USER_* information to the template
            $this->template->assign('BASE_USER_IS_LOGGED_IN', Me::isLoggedIn());
            $this->template->assign('BASE_USER_NICK', Me::getNick());
            $this->template->assign('BASE_USER_KARMA', Me::getKarma());
            $this->template->assign('BASE_USER_KARMA_PENDING', Me::getKarmaPending());
            $this->template->assign('BASE_USER_IS_ADMIN', Me::isAdmin());
            
            // Check if we should validate login
            if (isset($_POST['login-email'])) {
                Me::logIn();
            }

            // Analyze the query
            $this->queryAnalyze();
        }
    }
    
    /*
     * Methods for analyzing, reading and returning the query
     */
    
    private function queryAnalyze() {
        // Init array
        $this->query = [];

        // Split query
        $q = explode('/', Loader::getQuery());

        // Read fragments
        if (count($q) > 0) {
            foreach ($q as $v) {
                if (strlen($v) > 0) {
                    $this->query[] = $v;
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
        return $this->query;
    }
    protected function queryGetClean($prefix = '', $endfix = '') {
        if (count($this->query) > 0) {
            return $prefix . implode('/', $this->query) . $endfix;
        }
        else {
            return null;
        }
    }
    
    /*
     * Returning 404 page
     */
    
    protected function display404() {
        // New instance
        $controller = new NotFound();
    }
    
    /*
     * Close the database-connection and process queued cache
     */
    
    protected function close() {
        // Process cache
        CacheManager::store();

        // Close connection
        Database::close();
    }

    /*
     * Display message (if any)
     */

    private function showMessages() {
        // Keep the string here
        $ret = '';
        
        // Check if any message was found
        if (MessageManager::hasMessages()) {
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
        }
        
        // Check if any message was found
        if (strlen($ret) > 0) {
            $this->template->assign('SITE_MESSAGES', $ret);
        }
        else {
            $this->template->assign('SITE_MESSAGES', null);
        }
    }
    
    /*
     * Override default display method from Smarty
     */

    protected function displayAndCleanup($template, $sid = null) {
        // If develop, assign dev variables
        if (DEV) {
            $this->template->assign('DEV_QUERIES_NUM', Database::getCount());
            $this->template->assign('DEV_QUERIES', $this->cleanSqlLog($this->sqlLog));
            $this->template->assign('DEV_ELEMENT_COLLECTION', ElementCollection::getSize());
            $this->template->assign('DEV_CACHE_LOAD', CacheManager::getFetches());
        }
        
        // Close database and process cache
        $this->close();
        
        // Load message
        $this->showMessages();
        
        // Load cache
        $this->template->assign('TYPEAHEAD_CACHE_TIME', CacheManager::loadTypeaheadCache());
        
        // Display load time
        $time = \PHP_Timer::stop();
        $this->template->assign('TIMER', \PHP_Timer::secondsToTimeString($time));
        
        // Call Smarty
        $this->template->display($template, $sid);
    }
    
    /*
     * Clean up SQL log
     */
    
    private function cleanSqlLog($arr) {
        return '';
        // Some variables
        $str = '';
        $has_prepare = false;
        $prepare_val = [];
        
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
                    $str .= $temp_loc . '<pre>' . $temp_query . '</pre>';
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