<?php
/*
 * File: Frontpage.php
 * Holds: The frontpage view
 * Created: 02.10.13
 * Project: Youkok2
*/

namespace Youkok2\Views;

/*
 * Define what classes to use
 */

use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Models\Element as Element;
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;

/*
 * The Frontpage class, extending Base class
 */

class Frontpage extends Base {

    /*
     * Constructor
     */

    public function __construct($kill = false) {
        parent::__construct($kill);
        
        // Set view
        $this->addSiteData('view', 'frontpage');
        
        // Load default boxes
        $this->template->assign('HOME_NEWEST', Element::getNewest());
        $this->template->assign('HOME_MOST_POPULAR', Element::getMostPopular());
        
        // Check if this user is logged in
        if (Me::isLoggedIn()) {
            $this->template->assign('HOME_USER_LATEST', Me::loadLastDownloads());
            $this->template->assign('HOME_USER_FAVORITES', Element::getFavorites());
        }
        else {
            $this->loadInfobox();
        }
        
        // Assign other stuff
        $this->template->assign('HOME_MOST_POPULAR_DELTA', Me::getMostPopularDelta());
        
        // Display the template
        $this->displayAndCleanup('index.tpl');
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
        $get_file_number .= "WHERE is_directory = 0";
        
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