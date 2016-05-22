<?php
/*
 * File: FindDuplicates.php
 * Holds: Finds duplicates in the database
 * Created: 15.05.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors\Tasks;

use Youkok2\Models\Element;
use Youkok2\Processors\BaseProcessor;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;
use Youkok2\Collections\ElementCollection;

class FindDuplicates extends BaseProcessor
{

    /*
     * Override
     */

    protected function checkPermissions() {
        return $this->requireCli() or $this->requireAdmin();
    }
    
    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }

    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Method ran by the processor
     */
    
    public function run() {
        // Set code to 200
        $this->setData('code', 200);
        
        // Find duplicates by code
        $this->findDuplicatesByCourseCode();
        
        // Find duplicates by course name
        $this->findDuplicatesByCourseName();
    }
    
    private function findDuplicatesByCourseCode() {
        // Storing data
        $storage = [];
        $duplicates = [];

        // Get all courses
        $get_all_courses  = "SELECT id, name" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE directory = 1" . PHP_EOL;
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
        $this->setData('duplicates_code', $duplicates);
    }
    
    private function findDuplicatesByCourseName() {
        // Storing data
        $storage = [];
        $duplicates = [];

        // Get all courses
        $get_all_courses  = "SELECT id, name" . PHP_EOL;
        $get_all_courses .= "FROM archive" . PHP_EOL;
        $get_all_courses .= "WHERE directory = 1" . PHP_EOL;
        $get_all_courses .= "AND parent IS NULL";
        
        $get_all_courses_query = Database::$db->query($get_all_courses);
        while ($row = $get_all_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            // Split code and name
            $name_split = explode('||', $row['name']);
            
            // Add value
            if (isset($storage[$name_split[1]])) {
                $storage[$name_split[1]] = $storage[$name_split[1]] + 1;
            }
            else {
                $storage[$name_split[1]] = 1;
            }
        }
        
        // Loop and find duplicates
        foreach ($storage as $k => $v) {
            if ($v > 1) {
                $duplicates[] = $k;
            }
        }
        
        // Output
        $this->setData('duplicates_name', $duplicates);
    }
} 
