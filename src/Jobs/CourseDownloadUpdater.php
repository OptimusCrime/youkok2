<?php
/*
 * File: CourseDownloadUpdater.php
 * Holds: Job that updates the most popular downloads
 * Created: 29.11.2015
 * Project: Youkok2
*/

namespace Youkok2\Jobs;

use \Youkok2\Models\CourseDownloads as CourseDownloads;
use \Youkok2\Utilities\CacheManager as CacheManager;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Youkok2 as Youkok2;

class CourseDownloadUpdater extends Youkok2 {
    
    /*
     * Intervals for the query
     */
    
    public static $timeIntervals = [
        'WHERE a.is_visible = 1', // All
        'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.is_visible = 1', // Day
        'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND a.is_visible = 1', // Week 
        'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND a.is_visible = 1', // Month
        'WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR) AND a.is_visible = 1', // Year
    ];
        
    
    /*
     * Runs the actual job
     */
    
    public function run() {
        for ($i = 0; $i < 4; $i++) {
            $this->fetchCourseDownloads($i);
        }
    }
    
    /*
     * Called once the job is finished
     */
    
    public function done() {
        // Force cache manager to store all cache
        CacheManager::store();
    }
    
    /*
     * Fetch and store the actual data
     */
    
    private function fetchCourseDownloads($interval_index) {
        // For returning content
        $collection = [];
        
        // Load most popular files from the system
        $get_most_popular_courses  = "SELECT a.id, a.parent, COUNT(d.file) as 'downloaded_times'" . PHP_EOL;
        $get_most_popular_courses .= "FROM archive a" . PHP_EOL;
        $get_most_popular_courses .= "LEFT JOIN download AS d ON d.file = a.id" . PHP_EOL;
        $get_most_popular_courses .= CourseDownloadUpdater::$timeIntervals[$interval_index] . PHP_EOL;
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
        
        // Create a new instance of the model
        $course_downloads = new CourseDownloads();
        $course_downloads->setId($interval_index);
        $course_downloads->setData(json_encode($result_array));
        
        // Cache the element
        $course_downloads->cache();
    }
}