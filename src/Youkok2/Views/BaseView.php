<?php
/*
 * File: BaseView.php
 * Holds: Class extended by the other views
 * Created: 02.10.2013
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views;

use Youkok2\Youkok2;
use Youkok2\Models\Me;
use Youkok2\Models\Message;
use Youkok2\Utilities\BacktraceManager;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\ClassParser;
use Youkok2\Utilities\CsrfManager;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\JavaScriptLoader;
use Youkok2\Utilities\Loader;
use Youkok2\Utilities\MessageManager;
use Youkok2\Utilities\Routes;
use Youkok2\Utilities\TemplateHelper;

class BaseView
{

    /*
     * Internal variables
     */

    public $template;
    private $siteData;
    
    protected $application;
    protected $settings;
    protected $path;
    protected $me;
    
    public function __construct($app) {
        // Set reference to the application
        $this->application = $app;

        // Chech if this is a processors
        $class = explode('\\', get_class($this));
        foreach ($class as $v) {
            if ($v === 'Processors') {
                return;
            }
        }
        
        // Set some headers
        $this->application->setHeader('Content-Type', 'text/html; charset=utf-8');
        $this->application->setHeader('X-Powered-By', 'PHP/3.3.1');
        
        // Init template and assign the default tags
        $this->initTemplateEngine();
        
        // Only init stuff if we are not killing this view
        if ($this->getSetting('kill') !== true) {
            // Init the site itself
            $this->initSite();

            // Init the user object
            $this->initUser();
            
            // Set environment settings
            $this->setEnvSettings();
        }
    }
    
    /*
     * Init the template engine
     */

    private function initTemplateEngine() {
        // Init Smarty
        $this->template = new \Smarty();
        $this->template->left_delimiter = '[[+';
        $this->template->right_delimiter = ']]';
        
        // Assign the url_for method
        $this->template->registerClass('TemplateHelper', 'Youkok2\Utilities\TemplateHelper');

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

        // Assign query
        $this->template->assign('BASE_QUERY', $this->path);
    }

    /*
     * Init the site and check what we should do
     */

    private function initSite() {
        // Check if we're offline
        if (defined('AVAILABLE') and !AVAILABLE) {
            // We're offline, check if we should be allowed still
            if (!defined('AVAILABLE_WHITELIST') or (defined('AVAILABLE_WHITELIST') and
                    AVAILABLE_WHITELIST != $_SERVER['REMOTE_ADDR'])) {
                // Return error page
                $this->application->load(new ClassParser('Views\Error'), [
                    'kill' => true,
                    'reason' => 'unavailable',
                    'close_db' => $this->getSetting('close_db')
                ]);
                
                // Kill this views
                $this->settings['kill'] = true;
                
                // Return to avoid doing anything more
                return;
            }
        }

        // Trying to connect to the database
        if (Database::$db === null) {
            try {
                Database::connect();
            }
            catch (\Exception $e) {
                // Set db to null
                $this->db = null;

                // Tell the app to overwrite our current view
                $this->addSetting('overwrite', true);

                // Define what to run next
                $this->addSetting('overwrite_target', new ClassParser('Views\Error'));
                $this->addSetting('overwrite_settings', [
                    'kill' => true,
                    'reason' => 'db',
                    'close_db' => $this->getSetting('close_db')
                ]);
                
                // Kill this views
                $this->addSetting('kill', true);
                
                // Return to avoid doing anything more
                return;
            }
        }
        
        // Set some site data
        $this->addSiteData('view', 'general');
    }

    /*
     * Init the user objects and set various information
     */

    private function initUser() {
        // Check if we should skip this
        if ($this->getSetting('kill') === true) {
            return;
        }

        $this->me = new Me($this->application);

        // Add to site data
        $this->addSiteData('online', $this->me->isLoggedIn());
        
        // Set user information to the template
        $this->template->assign('USER_IS_LOGGED_IN', $this->me->isLoggedIn());
        $this->template->assign('USER_NICK', $this->me->getNick(false));
        $this->template->assign('USER_KARMA', $this->me->getKarma());
        $this->template->assign('USER_KARMA_PENDING', $this->me->getKarmaPending());
        $this->template->assign('USER_IS_ADMIN', $this->me->isAdmin());
        $this->template->assign('USER_IS_BANNED', $this->me->isBanned());
        $this->template->assign('USER_CAN_CONTRIBUTE', $this->me->canContribute());
        $this->template->assign(
            'USER_MOST_POPULAR_ELEMENT',
            $this->me->getModuleSettings('module1_delta')
        );
        $this->template->assign(
            'USER_MOST_POPULAR_COURSES',
            $this->me->getModuleSettings('module2_delta')
        );
        
        // Check if we should validate login
        if (isset($_POST['login-email'])) {
            $this->me->logIn();
        }
    }

    /*
     * Set various environment settings
     */

    private function setEnvSettings() {
        // Google Analytics
        if (USE_GA) {
            if ($this->me->isAdmin()) {
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
        
        // If develop, assign dev variables
        if (DEV) {
            $this->template->assign('DEV_QUERIES_NUM', Database::getQueryCount());
            $this->template->assign('DEV_CACHE_LOAD_NUM', CacheManager::getCount());
            $this->template->assign('DEV_QUERIES_BACKTRACE', Database::getQueryBacktrace());
            
            // Check if we're profiling
            if (defined('PROFILING') and PROFILING) {
                $this->template->assign('DEV_QUERIES_DURATION', Database::getProfilingDuration());
                $this->template->assign('DEV_CACHE_DURATION', CacheManager::getProfilingDuration());
            }
        }
        
        // Assign the js module list
        $this->template->assign('JS_MODULES', JavaScriptLoader::get());
        
        // Load message
        $this->template->assign('SITE_MESSAGES', MessageManager::get($this->application, $this->path));
        
        // Load cache
        $this->addSiteData('cache_time', CacheManager::loadTypeaheadCache());
        
        // Load site data
        $this->template->assign('SITE_DATA', addslashes(json_encode($this->siteData)));
        
        // Display load time
        $time = \PHP_Timer::stop();
        $this->template->assign('TIMER', \PHP_Timer::secondsToTimeString($time));
        
        // Fetch the smarty content
        $this->application->setBody($this->template->fetch($template, $sid));

        // Close database and process cache
        $this->close();
    }
    
    /*
     * Returning 404 page
     */
    
    protected function display404() {

        // Tell the app to overwrite our current view
        $this->addSetting('overwrite', true);

        // Define what to run next
        $this->addSetting('overwrite_target', new ClassParser('Views\NotFound'));
        if ($this->getSetting('close_db') === false) {
            $this->addSetting('overwrite_settings', [
                'close_db' => false
            ]);
        }

        // Kill this views
        $this->addSetting('kill', true);
    }
    
    /*
     * Close the database connection and process queued cache
     */
    
    protected function close() {
        // Process cache
        CacheManager::store();

        // Close connection
        if ($this->getSetting('close_db') !== false) {
            Database::close();
        }
    }
    
    /*
     * Static method used to check if a view is a processor or not
     */
    
    public static function isProcessor() {
        return false;
    }
    
    /*
     * Various setters for the view from the application
     */
    
    public function setSettings($settings) {
        $this->settings = $settings;
    }

    public function addSetting($key, $value) {
        $this->settings[$key] = $value;
    }
    
    public function getSetting($key) {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }
        return null;
    }
    
    public function getSettings() {
        return $this->settings;
    }

    public function setPath($path) {
        $this->path = $path;
    }
}
