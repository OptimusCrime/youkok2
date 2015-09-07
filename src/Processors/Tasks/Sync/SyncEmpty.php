<?php
/*
 * File: SyncEmpty.php
 * Holds: Syncs empty elements to their correct state
 * Created: 11.01.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors\Tasks\Sync;

use \Youkok2\Models\Course as Course;
use \Youkok2\Models\Element as Element;
use \Youkok2\Processors\Base as Base;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Utilities as Utilities;

class SyncEmpty extends Base {

    /*
     * Constructor
     */

    public function __construct($outputData = false, $returnData = false) {
        // Calling Base' constructor
        parent::__construct($outputData, $returnData);
        
        // Check access (only cli and admin)
        if (self::requireCli() or self::requireAdmin()) {
            // Check database
            if ($this->makeDatabaseConnection()) {
                // Sync
                $this->syncElements();
                
                // Close database connection
                Database::close();
            }
            else {
                $this->setError();
            }
        }
        else {
            // No access
            $this->noAccess();
        }
        
        // Handle output
        if ($this->outputData) {
            $this->outputData();
        }
        if ($this->returnData) {
            return $this->returnData();
        }
    }

    /*
     * Syncs empty courses
     */

    private function syncElements() {
        // Set code to 200
        $this->setData('code', 200);
        $update_num = 0;
        
        $get_all_directories  = "SELECT id, empty" . PHP_EOL;
        $get_all_directories .= "FROM archive";
        $get_all_directories .= "WHERE is_directory = 1";
        
        $get_all_directories_query = Database::$db->prepare($get_all_directories);
        $get_all_directories_query->execute();
        
        // Append to array
        while ($row = $get_all_directories_query->fetch(\PDO::FETCH_ASSOC)) {
            // Check if is actully empty
            $check_empty  = "SELECT id" . PHP_EOL;
            $check_empty .= "FROM archive" . PHP_EOL;
            $check_empty .= "WHERE parent = :parent" . PHP_EOL;
            $check_empty .= "AND is_visible = 1" . PHP_EOL;
            $check_empty .= "LIMIT 1";
            
            $check_empty_query = Database::$db->prepare($check_empty);
            $check_empty_query->execute(array(':parent' => $row['id']));
            $check = $check_empty_query->fetch(\PDO::FETCH_ASSOC);
            
            $update = false;
            if (isset($check['id']) and $row['empty'] == 1) {
                $update = true;
                $empty = false;
            }
            else if (!isset($check['id']) and $row['empty'] == 0) {
                $update = true;
                $empty = true;
            }
            
            if ($update) {
                $update_empty  = "UPDATE archive" . PHP_EOL;
                $update_empty .= "SET empty = :empty" . PHP_EOL;
                $update_empty .= "WHERE id = :id";
                
                $update_empty_query = Database::$db->prepare($update_empty);
                $update_empty_query->execute(array(':empty' => $empty, ':id' => $row['id']));
                
                $update_num++;
            }
        }
        
        $this->setData('updated', $update_num);
        
        // Check if we should clear cache
        if ($update_num > 0) {
            $this->runProcessor('tasks/clearcache');
        }
    }
} 