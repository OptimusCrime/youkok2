<?php
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

    public $template;
    private $siteData;
    
    protected $application;
    protected $settings;
    protected $path;
    protected $me;
    
    public function __construct($app) {
        $this->application = $app;

        // Chech if this is a processors
        $class = explode('\\', get_class($this));
        foreach ($class as $v) {
            if ($v === 'Processors') {
                return;
            }
        }
        
        $this->application->setHeader('Content-Type', 'text/html; charset=utf-8');
        $this->application->setHeader('X-Powered-By', 'PHP/3.3.1');
        
        $this->initTemplateEngine();
        
        if ($this->getSetting('kill') !== true) {
            $this->initSite();
            $this->initUser();
            $this->setEnvSettings();
        }
    }
    
    private function initTemplateEngine() {
        $this->template = new \Smarty();
        $this->template->left_delimiter = '[[+';
        $this->template->right_delimiter = ']]';
        
        $this->template->registerClass('TemplateHelper', 'Youkok2\Utilities\TemplateHelper');

        $this->template->setCompileDir(CACHE_PATH . '/smarty/compiled/');
        $this->template->setCacheDir(CACHE_PATH . '/smarty/cache/');

        $this->template->assign('VERSION', VERSION);
        $this->template->assign('DEV', DEV);
        $this->template->assign('OFFLINE', OFFLINE);
        $this->template->assign('SITE_URL', URL_FULL);
        $this->template->assign('SITE_TITLE', 'Den beste kokeboka på nettet');
        $this->template->assign('SITE_EMAIL_CONTACT', EMAIL_CONTACT);
        $this->template->assign('SEARCH_QUERY', '');
        $this->template->assign('HEADER_MENU', 'HOME');

        $this->template->assign('BASE_QUERY', $this->path);
    }

    private function initSite() {
        if (defined('AVAILABLE') and !AVAILABLE) {
            if (!defined('AVAILABLE_WHITELIST') or (defined('AVAILABLE_WHITELIST') and
                    AVAILABLE_WHITELIST != $_SERVER['REMOTE_ADDR'])) {
                // Tell the app to overwrite our current view
                $this->addSetting('overwrite', true);

                // Define what to run next
                $this->addSetting('overwrite_target', new ClassParser('Views\Error'));
                $this->addSetting('overwrite_settings', [
                    'kill' => true,
                    'reason' => 'unavailable',
                    'close_db' => $this->getSetting('close_db')
                ]);

                $this->addSetting('kill', true);
                
                return;
            }
        }

        if (Database::$db === null) {
            try {
                Database::connect();
            }
            catch (\Exception $e) {
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
                
                return;
            }
        }

        $this->addSiteData('view', 'general');
    }

    private function initUser() {
        if ($this->getSetting('kill') === true) {
            return;
        }

        $this->me = new Me($this->application);

        $this->addSiteData('online', $this->me->isLoggedIn());

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
        
        if (isset($_POST['login-email'])) {
            $this->me->logIn();
        }
    }

    private function setEnvSettings() {
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

        if (DEV) {
            $git_hash = exec('git rev-parse HEAD');
            $this->template->assign('GIT_HASH', $git_hash);
            $this->template->assign('GIT_HASH_SHORT', substr($git_hash, 0, 7));
        }

        if (defined('COMPRESS_ASSETS') and COMPRESS_ASSETS == false) {
            $this->template->assign('COMPRESS_ASSETS', false);
        }
        else {
            $this->template->assign('COMPRESS_ASSETS', true);
        }

        $this->template->assign('CSRF_TOKEN', htmlspecialchars(CsrfManager::getSignature()));
    }
    
    protected function addSiteData($key, $value) {
        $this->siteData[$key] = $value;
    }

    protected function displayAndCleanup($template, $sid = null) {
        if (DEV) {
            $this->template->assign('DEV_QUERIES_NUM', Database::getQueryCount());
            $this->template->assign('DEV_CACHE_LOAD_NUM', CacheManager::getCount());
            $this->template->assign('DEV_QUERIES_BACKTRACE', Database::getQueryBacktrace());
            
            if (defined('PROFILING') and PROFILING) {
                $this->template->assign('DEV_QUERIES_DURATION', Database::getProfilingDuration());
                $this->template->assign('DEV_CACHE_DURATION', CacheManager::getProfilingDuration());
            }
        }
        
        $this->addSiteData('cache_time', CacheManager::loadTypeaheadCache());
        
        $this->template->assign('JS_MODULES', JavaScriptLoader::get());
        $this->template->assign('SITE_MESSAGES', MessageManager::get($this->application, $this->path));
        $this->template->assign('SITE_DATA', addslashes(json_encode($this->siteData)));
        
        $time = \PHP_Timer::stop();
        $this->template->assign('TIMER', \PHP_Timer::secondsToTimeString($time));
        
        $this->application->setBody($this->template->fetch($template, $sid));

        $this->close();
    }
    
    protected function display404() {
        $this->addSetting('overwrite', true);
        $this->addSetting('overwrite_target', new ClassParser('Views\NotFound'));
        if ($this->getSetting('close_db') === false) {
            $this->addSetting('overwrite_settings', [
                'close_db' => false
            ]);
        }

        $this->addSetting('kill', true);
    }
    
    protected function close() {
        CacheManager::store();

        if ($this->getSetting('close_db') !== false) {
            Database::close();
        }
    }

    public static function isProcessor() {
        return false;
    }
    
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
