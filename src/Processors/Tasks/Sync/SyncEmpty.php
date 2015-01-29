<?php
/*
 * File: SyncEmpty.php
 * Holds: Syncs empty elements to their correct state
 * Created: 11.01.15
 * Project: Youkok2
*/

namespace Youkok2\Processors\Tasks\Sync;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Course as Course;
use \Youkok2\Models\Element as Element;
use \Youkok2\Processors\Base as Base;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Utilities as Utilities;

/*
 * LoadCourses extending Base
 */

class SyncEmpty extends Base {

    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
        if (self::requireCli() or self::requireAdmin()) {
            // Check database
            if ($this->checkDatabase()) {
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
        
        // Return data
        $this->returnData();
    }

    /*
     * Check if we can connect to the database
     */

    private function checkDatabase() {
        try {
            Database::connect();

            return true;
        }
        catch (Exception $e) {
            $this->setData('code', 500);
            $this->setData('msg', 'Could not connect to database');

            return false;
        }
    }

    /*
     * Syncs empty courses
     */

    private function syncElements() {
        // Set code to 200
        $this->setData('code', 200);
        $update_num = 0;
        
        $get_all_elements  = "SELECT id, empty" . PHP_EOL;
        $get_all_elements .= "FROM archive";
        
        $get_all_elements_query = Database::$db->prepare($get_all_elements);
        $get_all_elements_query->execute();
        
        // Append to array
        while ($row = $get_all_elements_query->fetch(\PDO::FETCH_ASSOC)) {
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
        
        // Check if we should clear cache
        if ($update_num > 0) {
            $this->runProcessor('tasks/clearcache');
        }
    }
} 