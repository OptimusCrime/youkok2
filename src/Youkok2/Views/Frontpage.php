<?php
/*
 * File: Frontpage.php
 * Holds: The frontpage view
 * Created: 02.10.2013
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Models\CourseDownloads;
use Youkok2\Models\Me;
use Youkok2\Models\Cache\MeDownloads;
use Youkok2\Utilities\Database;

class Frontpage extends BaseView
{
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Run the view
     */

    public function run() {        
        // Make sure to kill the view if something broke
        if ($this->getSetting('kill') === true) {
            return;
        }
        
        // Set view
        $this->addSiteData('view', 'frontpage');
        
        // Load default boxes
        $this->template->assign('HOME_NEWEST', Element::getNewest());
        $this->template->assign('HOME_MOST_POPULAR_ELEMENTS', $this->application->runProcessor('/module/get', ['module' => 1, 'encode' => false])->getData()['data']);
        $this->template->assign('HOME_MOST_POPULAR_COURSES', $this->application->runProcessor('/module/get', ['module' => 2, 'encode' => false])->getData()['data']);
        $this->template->assign('HOME_LAST_VISITED', Element::getLastVisitedElements());
        
        // Check if this user is logged in
        if (Me::isLoggedIn()) {
            $this->template->assign('HOME_USER_FAVORITES', Me::getFavorites());
            $this->template->assign('HOME_USER_LATEST', MeDownloads::get());
        }
        else {
            $this->loadInfobox();
        }
        
        // Display the template
        $this->displayAndCleanup('frontpage.tpl');
    }
    
    /*
     * Method for loading infobox (users not logged in)
     */
    
    private function loadInfobox() {
        // Load users
        $get_user_number  = "SELECT COUNT(id) AS 'num_users'" . PHP_EOL;
        $get_user_number .= "FROM user";
        
        $get_user_number_query = Database::$db->query($get_user_number);
        $get_user_number_result = $get_user_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Load files
        $get_file_number  = "SELECT COUNT(id) AS 'num_files'" . PHP_EOL;
        $get_file_number .= "FROM archive" . PHP_EOL;
        $get_file_number .= "WHERE directory = 0" . PHP_EOL;
        $get_file_number .= "AND pending = 0" . PHP_EOL;
        $get_file_number .= "AND deleted = 0";
        
        $get_file_number_query = Database::$db->query($get_file_number);
        $get_file_number_result = $get_file_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Load downloads
        $get_download_number  = "SELECT COUNT(id) AS 'num_downloads'" . PHP_EOL;
        $get_download_number .= "FROM download";
        
        $get_download_number_query = Database::$db->query($get_download_number);
        $get_dowload_number_result = $get_download_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Return text
        $this->template->assign('HOME_STATS_USERS', number_format($get_user_number_result['num_users']));
        $this->template->assign('HOME_STATS_FILES', number_format($get_file_number_result['num_files']));
        $this->template->assign('HOME_STATS_DOWNLOADS', number_format($get_dowload_number_result['num_downloads']));
    }
}
