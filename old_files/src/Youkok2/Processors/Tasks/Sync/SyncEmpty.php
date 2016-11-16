<?php
namespace Youkok2\Processors\Tasks\Sync;

use Youkok2\Models\Course;
use Youkok2\Models\Element;
use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;

class SyncEmpty extends BaseProcessor
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
        $update_num = 0;
        
        $get_all_directories  = "SELECT id, empty" . PHP_EOL;
        $get_all_directories .= "FROM archive" . PHP_EOL;
        $get_all_directories .= "WHERE directory = 1";
        
        $get_all_directories_query = Database::$db->prepare($get_all_directories);
        $get_all_directories_query->execute();
        
        while ($row = $get_all_directories_query->fetch(\PDO::FETCH_ASSOC)) {
            $check_empty  = "SELECT id" . PHP_EOL;
            $check_empty .= "FROM archive" . PHP_EOL;
            $check_empty .= "WHERE parent = :parent" . PHP_EOL;
            $check_empty .= "AND deleted = 0" . PHP_EOL;
            $check_empty .= "AND pending = 0" . PHP_EOL;
            $check_empty .= "LIMIT 1";
            
            $check_empty_query = Database::$db->prepare($check_empty);
            $check_empty_query->execute([':parent' => $row['id']]);
            $check = $check_empty_query->fetch(\PDO::FETCH_ASSOC);
            
            $update = false;
            if (isset($check['id']) and $row['empty'] == 1) {
                $update = true;
                $empty = false;
            }
            elseif (!isset($check['id']) and $row['empty'] == 0) {
                $update = true;
                $empty = true;
            }
            
            if ($update) {
                $update_empty  = "UPDATE archive" . PHP_EOL;
                $update_empty .= "SET empty = :empty" . PHP_EOL;
                $update_empty .= "WHERE id = :id";
                
                $update_empty_query = Database::$db->prepare($update_empty);
                $update_empty_query->execute([':empty' => $empty, ':id' => $row['id']]);
                
                $update_num++;
            }
        }
        
        $this->setData('updated', $update_num);
        
        if ($update_num > 0) {
            $this->application->runProcessor('tasks/clearcache', []);
        }
    }
}
