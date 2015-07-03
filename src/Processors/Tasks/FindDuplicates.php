<?php
/*
 * File: FindDuplicates.php
 * Holds: Finds duplicates in the database
 * Created: 15.05.2015
 * Project: Youkok2
*/

namespace Youkok2\Processors\Tasks;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Element as Element;
use \Youkok2\Processors\BaseProcessor as BaseProcessor;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Utilities as Utilities;
use \Youkok2\Collections\ElementCollection as ElementCollection;

/*
 * FindDuplicates extending Base
 */

class FindDuplicates extends BaseProcessor {
    
    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
        if (self::requireCli() or self::requireAdmin()) {
            // Check if we should turn on buffering
            if (!self::requireCli()) {
                ob_start();
            }
            
            // Check database
            if ($this->checkDatabase()) {
                // Reset
                $this->resetExamData();
                
                // Fetch
                $this->fetch();
                
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
     * Reset exam information here
     */
    
    private function resetExamData() {
        $reset_exam  = "UPDATE archive" . PHP_EOL;
        $reset_exam .= "SET exam = NULL" . PHP_EOL;
        
        Database::$db->query($reset_exam);
    }

    /*
     * Fetch the actual data here
     */

    private function fetch() {
        // Set code to 200
        $this->setData('code', 200);
        
        // Storing data
        $storage = [];
        $duplicates = [];

        // Get all exames
        $get_all_courses  = "SELECT id, name" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE is_directory = 1" . PHP_EOL;
        $get_all_courses .= "AND parent IS NULL";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            // Split code and name
            $name_split = explode('||', $row['name']);
            
            // Add value
            if (isset($storage[$name_split[0]])) {
                $storage[$name_split[0]] = $storage[$name_split[0]] + 1;
            }
            else {
                $storage[$name_split[0]] = 1;
            }
        }
        
        // Loop and find duplicates
        foreach ($storage as $k => $v) {
            if ($v > 1) {
                $duplicates[] = $k;
            }
        }
        
        // Output
        $this->setData('duplicates', $duplicates);
    }
} 