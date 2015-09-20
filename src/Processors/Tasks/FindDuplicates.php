<?php
/*
 * File: FindDuplicates.php
 * Holds: Finds duplicates in the database
 * Created: 15.05.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors\Tasks;

use \Youkok2\Models\Element as Element;
use \Youkok2\Processors\BaseProcessor as BaseProcessor;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Utilities as Utilities;
use \Youkok2\Collections\ElementCollection as ElementCollection;

class FindDuplicates extends BaseProcessor {

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
     * Construct
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
    }
    
    /*
     * Method ran by the processor
     */
    
    public function run() {
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
        
        // Check if we should just dump everything for HTML view
        if (isset($_GET['format']) and $_GET['format'] == 'html') {
            print_r($duplicates);
        }
        
        // Output
        $this->setData('duplicates', $duplicates);
    }
} 