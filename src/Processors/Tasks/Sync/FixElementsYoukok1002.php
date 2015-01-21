<?php
/*
 * File: FixElementsYoukok1002.php
 * Holds: One time setup for prepearing for Youkok1002
 * Created: 21.01.15
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

class FixElementsYoukok1002 extends Base {

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
                $this->updateOwners();
                
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
     * Set owners for their archive elements
     */

    private function updateOwners() {
        $get_all_elements  = "SELECT id" . PHP_EOL;
        $get_all_elements .= "FROM archive";
        
        $get_all_elements_query = Database::$db->prepare($get_all_elements);
        $get_all_elements_query->execute();
        
        // Append to array
        while ($row = $get_all_elements_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get owner of flag
            $get_owner  = "SELECT user" . PHP_EOL;
            $get_owner .= "FROM flag" . PHP_EOL;
            $get_owner .= "WHERE file = :file" . PHP_EOL;
            $get_owner .= "LIMIT 1";
            
            $get_owner_query = Database::$db->prepare($get_owner);
            $get_owner_query->execute(array(':file' => $row['id']));
            $owner = $get_owner_query->fetch(\PDO::FETCH_ASSOC);
            
            // Check if we found the owner
            if (isset($owner['user'])) {
                // Owner was found, store in archive element
                $set_owner  = "UPDATE archive" . PHP_EOL;
                $set_owner .= "SET owner = :owner" . PHP_EOL;
                $set_owner .= "WHERE id = :id";
                
                $set_owner_query = Database::$db->prepare($set_owner);
                $set_owner_query->execute(array(':owner' => $owner['user'], ':id' => $row['id']));
            }
        }
    }
} 