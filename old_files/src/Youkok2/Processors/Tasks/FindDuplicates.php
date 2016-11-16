<?php
namespace Youkok2\Processors\Tasks;

use Youkok2\Models\Element;
use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;
use Youkok2\Collections\ElementCollection;

class FindDuplicates extends BaseProcessor
{
    
    protected function checkPermissions() {
        return $this->requireCli() or $this->requireAdmin();
    }

    protected function requireDatabase() {
        return true;
    }
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public function run() {
        $this->setData('code', 200);
        
        $this->findDuplicatesByCourseCode();
        $this->findDuplicatesByCourseName();
    }
    
    private function findDuplicatesByCourseCode() {
        $storage = [];
        $duplicates = [];

        $get_all_courses  = "SELECT id, name" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE directory = 1" . PHP_EOL;
        $get_all_courses .= "AND parent IS NULL";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            $name_split = explode('||', $row['name']);
            
            if (isset($storage[$name_split[0]])) {
                $storage[$name_split[0]] = $storage[$name_split[0]] + 1;
            }
            else {
                $storage[$name_split[0]] = 1;
            }
        }
        
        foreach ($storage as $k => $v) {
            if ($v > 1) {
                $duplicates[] = $k;
            }
        }

        $this->setData('duplicates_code', $duplicates);
    }
    
    private function findDuplicatesByCourseName() {
        $storage = [];
        $duplicates = [];

        $get_all_courses  = "SELECT id, name" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE directory = 1" . PHP_EOL;
        $get_all_courses .= "AND parent IS NULL";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            $name_split = explode('||', $row['name']);
            
            if (isset($storage[$name_split[1]])) {
                $storage[$name_split[1]] = $storage[$name_split[1]] + 1;
            }
            else {
                $storage[$name_split[1]] = 1;
            }
        }
        
        foreach ($storage as $k => $v) {
            if ($v > 1) {
                $duplicates[] = $k;
            }
        }
        
        $this->setData('duplicates_name', $duplicates);
    }
}
