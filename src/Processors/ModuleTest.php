<?php
/*
 * File: ModuleTest.php
 * Holds: WIP
 * Created: 20.11.2015
 * Project: Youkok2
 * 
*/

namespace Youkok2\Processors;

use \Youkok2\Models\Element as Element;
use \Youkok2\Models\Me as Me;
use \Youkok2\Models\Controllers\ElementController as ElementController;
use \Youkok2\Utilities\Database as Database;

class ModuleTest extends BaseProcessor {
    
    /*
     * Override
     */

    protected function canBeLoggedIn() {
        return true;
    }
    
    /*
     * Override
     */

    protected function requireDatabase() {
        return true;
    }

    /*
     * Constructor
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
    }
    
    /*
     * Fetch module data
     */
    
    public function run() {
        // For returning content
        $collection = [];
        
        // Load most popular files from the system
        $get_most_popular_courses  = "SELECT a.id, a.parent, COUNT(d.file) as 'downloaded_times'" . PHP_EOL;
        $get_most_popular_courses .= "FROM archive a" . PHP_EOL;
        $get_most_popular_courses .= "LEFT JOIN download AS d ON d.file = a.id" . PHP_EOL;
        $get_most_popular_courses .= "WHERE a.is_visible = 1" . PHP_EOL;
        $get_most_popular_courses .= "GROUP BY a.id" . PHP_EOL;
        
        $get_most_popular_courses_query = Database::$db->prepare($get_most_popular_courses);
        $get_most_popular_courses_query->execute();
        while ($row = $get_most_popular_courses_query->fetch(\PDO::FETCH_ASSOC)) {
            $collection['element_' . $row['id']] = [
                'id' => $row['id'],
                'downloaded' => $row['downloaded_times'],
                'parent' => $row['parent']];
        }
        
        // Propagate the values to the parents
        while (true) {

            $found_child = false;
            // Loop the collection
            foreach ($collection as $k => $v) {
                // Only handle children
                if ($v['parent'] != null) {
                    // Check if this has a downloaded value
                    if ($v['downloaded'] > 0) {
                        $found_child = true;
                    }
                    
                    // Increase the number of downloades to the parent
                    if (isset($collection['element_' . $v['parent']])) {
                        $collection['element_' . $v['parent']]['downloaded'] += $v['downloaded'];
                    }
                    
                    // Reset own downloaded
                    $collection[$k]['downloaded'] = 0;
                }
            }
            
            // Check if we should break the loop
            if (!$found_child) {
                break;
            }
        }
        
        // Prettify the results
        $collection_clean = [];
        foreach ($collection as $k => $v) {
            if ($v['downloaded'] > 0) {
                $collection_clean[$v['id']] = $v['downloaded'];
            }
        }
        
        // Sort the result
        arsort($collection_clean);
        
        // Get the 15 best results (if any)
        $result_array = [];
        $i = 0;
        foreach ($collection_clean as $k => $v) {
            // Add to result array
            $result_array[] = [
                'id' => $k,
                'downloaded' => $v
            ];
            
            // Check if we should break the loop
            $i++;
            
            if ($i == 15) {
                break;
            }
        }

        // Set the data
        $this->setData('data', $result_array);
        
        // Set ok
        $this->setOK();
    }
}