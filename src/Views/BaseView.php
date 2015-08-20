<?php
/*
 * File: BaseView.php
 * Holds: Class extended by the other views
 * Created: 02.10.2013
 * Project: Youkok2
*/

namespace Youkok2\Views;

/*
 * Define what classes to use
 */

use \Youkok2\Youkok2 as Youkok2;
use \Youkok2\Models\Me as Me;
use \Youkok2\Models\Message as Message;
use \Youkok2\Utilities\BacktraceManager as BacktraceManager;
use \Youkok2\Utilities\CacheManager as CacheManager;
use \Youkok2\Utilities\CsrfManager as CsrfManager;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\JavaScriptLoader as JavaScriptLoader;
use \Youkok2\Utilities\Loader as Loader;
use \Youkok2\Utilities\MessageManager as MessageManager;
use \Youkok2\Utilities\Routes as Routes;

/*
 * Class that all the controllers extends
 */

class BaseView extends Youkok2 {

    /*
     * Internal variables
     */

    public $template;
    private $siteData;
    
    /*
     * Constructor
     */

    public function __construct($kill = false) {
        // Init template and assign the default tags
        $this->initTemplateEngine();

        // If we should kill the script, then we do so here
        if ($kill) {
            return;
        }

        // Init the site itself
        $this->initSite();

        // Init the user object
        $this->initUser();

        // Set environment settings
        $this->setEnvSettings();
    }
    
    /*
     * Init the template engine
     */

    private function initTemplateEngine() {
        // Init Smarty
        $this->template = new \Smarty();
        $this->template->left_delimiter = '[[+';
        $this->template->right_delimiter = ']]';

        // Set caching and compile dir
        $this->template->setCompileDir(CACHE_PATH . '/smarty/compiled/');
        $this->template->setCacheDir(CACHE_PATH . '/smarty/cache/');

        // Define a few constants in Smarty
        $this->template->assign('VERSION', VERSION);
        $this->template->assign('DEV', DEV);
        $this->template->assign('OFFLINE', OFFLINE);
        $this->template->assign('SITE_URL', URL_FULL);
        $this->template->assign('SITE_TITLE', 'Den beste kokeboka på nettet');
        $this->template->assign('SITE_EMAIL_CONTACT', EMAIL_CONTACT);
        $this->template->assign('SEARCH_QUERY', '');
        $this->template->assign('HEADER_MENU', 'HOME');

        // Route variables
        $this->template->assign('ROUTE_ARCHIVE', Routes::ARCHIVE);
        $this->template->assign('ROUTE_DOWNLOAD', Routes::DOWNLOAD);
        $this->template->assign('ROUTE_REDIRECT', Routes::REDIRECT);
        $this->template->assign('ROUTE_PROCESSOR', Routes::PROCESSOR);

        // Assign query
        $this->template->assign('BASE_QUERY', Loader::getQuery());
    }

    /*
     * Init the site and check what we should do
     */

    private function initSite() {
        // Check if we're offline
        if (defined('AVAILABLE') and !AVAILABLE) {
            // We're offline, check if we should be allowed still
            if (!defined('AVAILABLE_WHITELIST') or (defined('AVAILABLE_WHITELIST') and AVAILABLE_WHITELIST != $_SERVER['REMOTE_ADDR'])) {
                // Not whitelisted, kill
                new Error('unavailable');
                die();
            }
        }

        // Trying to connect to the database
        try {
            Database::connect();
        }
        catch (\Exception $e) {
            $this->db = null;

            new Error('db');
            die();
        }
        
        // Set some site data
        $this->addSiteData('view', 'general');
    }

    /*
     * Init the user objects and set various information
     */

    private function initUser() {
        // Init the user
        Me::init();

        // Add to site data
        $this->addSiteData('online', Me::isLoggedIn());

        // Set BASE_USER_* information to the template
        $this->template->assign('USER_IS_LOGGED_IN', Me::isLoggedIn());
        $this->template->assign('USER_NICK', Me::getNick());
        $this->template->assign('USER_KARMA', Me::getKarma());
        $this->template->assign('USER_KARMA_PENDING', Me::getKarmaPending());
        $this->template->assign('USER_IS_ADMIN', Me::isAdmin());
        $this->template->assign('USER_IS_BANNED', Me::isBanned());
        $this->template->assign('USER_CAN_CONTRIBUTE', Me::canContribute());
        $this->template->assign('USER_MOST_POPULAR_DELTA', Me::getMostPopularDelta());

        // Check if we should validate login
        if (isset($_POST['login-email'])) {
            Me::logIn();
        }
    }

    /*
     * Set various environment settings
     */

    private function setEnvSettings() {
        // Google Analytics
        if (USE_GA) {
            if (Me::isAdmin()) {
                $this->template->assign('SITE_USE_GA', false);
            }
            else {
                $this->template->assign('SITE_USE_GA', true);
            }
        }
        else {
            $this->template->assign('SITE_USE_GA', false);
        }

        // Version with Git hash
        if (DEV) {
            $git_hash = exec('git rev-parse HEAD');
            $this->template->assign('GIT_HASH', $git_hash);
            $this->template->assign('GIT_HASH_SHORT', substr($git_hash, 0, 7));
        }

        // Use compression
        if (defined('COMPRESS_ASSETS') and COMPRESS_ASSETS == false) {
            $this->template->assign('COMPRESS_ASSETS', false);
        }
        else {
            $this->template->assign('COMPRESS_ASSETS', true);
        }

        $this->template->assign('CSRF_TOKEN', htmlspecialchars(CsrfManager::getSignature()));
    }
    
    /*
     * Add data to the json object displayed at all pages
     */
    
    protected function addSiteData($key, $value) {
        $this->siteData[$key] = $value;
    }
    
    /*
     * Override default display method from Smarty
     */

    protected function displayAndCleanup($template, $sid = null) {
        // Get messages
        $messages = Message::getMessages(Loader::getQuery());
        
        // Check (and handle) message
        if (count($messages) > 0) {
            foreach ($messages as $message) {
                MessageManager::addMessage($message->getMessage(), $message->getType(), true);
            }
        }
        
        // If develop, assign dev variables
        if (DEV) {
            $this->template->assign('DEV_QUERIES_NUM', Database::getQueryCount());
            $this->template->assign('DEV_CACHE_LOAD_NUM', CacheManager::getCount());
            $this->template->assign('DEV_QUERIES_BACKTRACE', Database::getQueryBacktrace());
        }
        
        // Assign the js module list
        $this->template->assign('JS_MODULES', JavaScriptLoader::get());
        
        // Load message
        $this->showMessages();
        
        // Load cache
        $this->addSiteData('cache_time', CacheManager::loadTypeaheadCache());
        
        // Load site data
        $this->template->assign('SITE_DATA', addslashes(json_encode($this->siteData)));
        
        // Display load time
        $time = \PHP_Timer::stop();
        $this->template->assign('TIMER', \PHP_Timer::secondsToTimeString($time));
        
        // Call Smarty
        $this->template->display($template, $sid);

        // Close database and process cache
        $this->close();
    }
    
    /*
     * Returning 404 page
     */
    
    protected function display404() {
        // New instance
        new NotFound();
    }
    
    /*
     * Close the database connection and process queued cache
     */
    
    protected function close() {
        // Process cache
        CacheManager::store();

        // Close connection
        Database::close();
    }
    
    /*
     * Display message (if any) TODO REMOVE
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
}