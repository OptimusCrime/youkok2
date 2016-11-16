<?php
namespace Youkok2\Views;

use Youkok2\Models\Element;
use Youkok2\Models\CourseDownloads;
use Youkok2\Models\Me;
use Youkok2\Models\Cache\MeDownloads;
use Youkok2\Utilities\Database;

class Frontpage extends BaseView
{
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        // Make sure to kill the view if something broke
        if ($this->getSetting('kill') === true) {
            return;
        }
        
        $this->addSiteData('view', 'frontpage');
        
        $this->template->assign('HOME_NEWEST', Element::getNewest());
        $this->template->assign(
            'HOME_MOST_POPULAR_ELEMENTS',
            $this->application->runProcessor(
                '/module/get',
                [
                    'module' => 1,
                    'encode' => false,
                    'output' => false,
                    'application' => false
                ]
            )->getData()['data']
        );
        $this->template->assign(
            'HOME_MOST_POPULAR_COURSES',
            $this->application->runProcessor(
                '/module/get',
                [
                    'module' => 2,
                    'encode' => false,
                    'output' => false,
                    'application' => false
                ]
            )->getData()['data']
        );

        $this->template->assign('HOME_LAST_VISITED', Element::getLastVisitedElements());
        
        if ($this->me->isLoggedIn()) {
            $this->template->assign('HOME_USER_FAVORITES', $this->me->getFavorites());
            $this->template->assign('HOME_USER_LATEST', MeDownloads::get($this->me));
        }
        else {
            $this->loadInfobox();
        }
        
        $this->displayAndCleanup('frontpage.tpl');
    }
    
    private function loadInfobox() {
        $get_user_number  = "SELECT COUNT(id) AS 'num_users'" . PHP_EOL;
        $get_user_number .= "FROM user";
        
        $get_user_number_query = Database::$db->query($get_user_number);
        $get_user_number_result = $get_user_number_query->fetch(\PDO::FETCH_ASSOC);
        
        $get_file_number  = "SELECT COUNT(id) AS 'num_files'" . PHP_EOL;
        $get_file_number .= "FROM archive" . PHP_EOL;
        $get_file_number .= "WHERE directory = 0" . PHP_EOL;
        $get_file_number .= "AND pending = 0" . PHP_EOL;
        $get_file_number .= "AND deleted = 0";
        
        $get_file_number_query = Database::$db->query($get_file_number);
        $get_file_number_result = $get_file_number_query->fetch(\PDO::FETCH_ASSOC);
        
        $get_download_number  = "SELECT COUNT(id) AS 'num_downloads'" . PHP_EOL;
        $get_download_number .= "FROM download";
        
        $get_download_number_query = Database::$db->query($get_download_number);
        $get_dowload_number_result = $get_download_number_query->fetch(\PDO::FETCH_ASSOC);

        $this->template->assign('HOME_STATS_USERS', number_format($get_user_number_result['num_users']));
        $this->template->assign('HOME_STATS_FILES', number_format($get_file_number_result['num_files']));
        $this->template->assign('HOME_STATS_DOWNLOADS', number_format($get_dowload_number_result['num_downloads']));
    }
}
