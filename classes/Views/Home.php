<?php
/*
 * File: Home.php
 * Holds: The frontpage
 * Created: 02.10.13
 * Project: Youkok2
*/

namespace Youkok2\Views;

use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Models\Me as Me;
use \Youkok2\Shared\Elements as Elements;
use \Youkok2\Utilities\Database as Database;

/*
 * The Home class, extending Base class
 */

class Home extends Base {

    /*
     * Constructor
     */

    public function __construct($kill = false) {
        parent::__construct();
        
        // Load default boxes
        $this->template->assign('HOME_NEWEST', Elements::getNewest());
        $this->template->assign('HOME_MOST_POPULAR', Elements::getMostPopular());
        
        // Check if this user is logged in
        if (Me::isLoggedIn()) {
            $this->template->assign('HOME_USER_LATEST', Me::loadLastDownloads());
            $this->template->assign('HOME_USER_FAVORITES', Elements::getFavorites());
        }
        else {
            $this->template->assign('HOME_INFOBOX', $this->loadInfobox());
        }
        
        // Assign other stuff
        $this->template->assign('HOME_MOST_POPULAR_DELTA', Me::getUserDelta());
        
        // Display the template
        $this->displayAndCleanup('index.tpl');
    }
    
    //
    // Method for loading infobox (users not logged in)
    //
    
    private function loadInfobox() {
        // Load users
        $get_user_number = "SELECT COUNT(id) as 'antall_brukere'
        FROM user";
        
        $get_user_number_query = Database::$db->prepare($get_user_number);
        $get_user_number_query->execute();
        $get_user_number_result = $get_user_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Load files
        $get_file_number = "SELECT COUNT(id) as 'antall_filer'
        FROM archive
        WHERE is_directory = 0";
        
        $get_file_number_query = Database::$db->prepare($get_file_number);
        $get_file_number_query->execute();
        $get_file_number_result = $get_file_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Load downloads
        $get_download_number = "SELECT COUNT(id) as 'antall_nedlastninger'
        FROM download";
        
        $get_download_number_query = Database::$db->prepare($get_download_number);
        $get_download_number_query->execute();
        $get_dowload_number_result = $get_download_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Return text
        return '<p>Vi har for tiden <b>' . number_format($get_user_number_result['antall_brukere']) . '</b> registrerte brukere, <b>' . number_format($get_file_number_result['antall_filer']) . '</b> filer og totalt <b>' . number_format($get_dowload_number_result['antall_nedlastninger']) . '</b> nedlastninger i vårt system.</p>';
    }
}