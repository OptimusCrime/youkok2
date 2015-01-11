<?php
/*
 * File: FixElementsYoukok100.php
 * Holds: One time setup for prepearing for Youkok100
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

class FixElementsYoukok100 extends Base {

    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
        if (self::requireCli() or self::requireAdmin()) {
            // Check database
            if ($this->checkDatabase()) {
                // Update parents to match the new setup
                $this->updateParents();
                
                // Delete root element
                $this->deleteRoot();
                
                // Close database connection
                Database::close();
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
     * Sets parent = NULL where parent = 1
     */

    private function updateParents() {
        $update_parents  = "UPDATE archive" . PHP_EOL;
        $update_parents  = "SET parent = NULL" . PHP_EOL;
        $update_parents .= "WHERE parent = 1";
        
        Database::$db->query($update_parents);
    }
    
    /*
     * Delete root element
     */

    private function deleteRoot() {
        $delete_root  = "DELETE FROM archive" . PHP_EOL;
        $delete_root .= "WHERE id = 1";
        
       Database::$db->query($delete_root);
    }
} 