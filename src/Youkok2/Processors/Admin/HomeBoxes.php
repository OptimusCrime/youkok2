<?php
namespace Youkok2\Processors\Admin;

use Youkok2\Processors\BaseProcessor;
use Youkok2\Models\Me;
use Youkok2\Collections\ElementCollection;
use Youkok2\Utilities\Database;

class HomeBoxes extends BaseProcessor
{

    protected function requireDatabase() {
        return true;
    }

    protected function checkPermissions() {
        return $this->requireAdmin();
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function run() {
        $get_admin_downloads  = "SELECT COUNT(id) AS 'num_downloads'" . PHP_EOL;
        $get_admin_downloads .= "FROM download";
        
        $get_admin_downloads_query = Database::$db->query($get_admin_downloads);
        $get_admin_downloads_result = $get_admin_downloads_query->fetch(\PDO::FETCH_ASSOC);
        
        $get_admin_users  = "SELECT COUNT(id) AS 'num_users'" . PHP_EOL;
        $get_admin_users .= "FROM user";
        
        $get_admin_users_query = Database::$db->query($get_admin_users);
        $get_admin_users_result = $get_admin_users_query->fetch(\PDO::FETCH_ASSOC);
        
        $get_admin_files  = "SELECT COUNT(id) AS 'num_files'" . PHP_EOL;
        $get_admin_files .= "FROM archive" . PHP_EOL;
        $get_admin_files .= "WHERE directory = 0" . PHP_EOL;
        $get_admin_files .= "AND pending = 0";
        
        $get_admin_files_query = Database::$db->query($get_admin_files);
        $get_admin_files_result = $get_admin_files_query->fetch(\PDO::FETCH_ASSOC);
        
        $get_admin_courses  = "SELECT COUNT(id) AS 'num_courses'" . PHP_EOL;
        $get_admin_courses .= "FROM archive" . PHP_EOL;
        $get_admin_courses .= "WHERE parent is NULL" . PHP_EOL;
        $get_admin_courses .= "AND pending = 0";
        
        $get_admin_courses_query = Database::$db->query($get_admin_courses);
        $get_admin_courses_result = $get_admin_courses_query->fetch(\PDO::FETCH_ASSOC);
        
        $result = [
            'downloads' => number_format($get_admin_downloads_result['num_downloads']),
            'users' => number_format($get_admin_users_result['num_users']),
            'files' => number_format($get_admin_files_result['num_files']),
            'courses' => number_format($get_admin_courses_result['num_courses']),
        ];
        
        $this->setData('data', $result);
        
        $this->setOK();
    }
}
