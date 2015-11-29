<?php
/*
 * File: CourseDownloadUpdater.php
 * Holds: Job that updates the most popular downloads
 * Created: 29.11.2015
 * Project: Youkok2
*/

namespace Youkok2\Jobs;

use \Youkok2\Models\CourseDownloads as CourseDownloads;
use \Youkok2\Models\Controllers\CourseDownloadsController as CourseDownloadsController;
use \Youkok2\Utilities\CacheManager as CacheManager;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Youkok2 as Youkok2;

class CourseDownloadUpdater extends Youkok2 {
    
    /*
     * Runs the actual job
     */
    
    public function run() {
        for ($i = 0; $i <= 4; $i++) {
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
        
        // Load the courses
        $get_all_downloaded_data  = "SELECT a.id, a.parent" . PHP_EOL;
        $get_all_downloaded_data .= "FROM archive a" . PHP_EOL;
        $get_all_downloaded_data .= "WHERE a.is_visible = 1";
        
        $get_all_downloaded_data_query = Database::$db->prepare($get_all_downloaded_data);
        $get_all_downloaded_data_query->execute();
        while ($row = $get_all_downloaded_data_query->fetch(\PDO::FETCH_ASSOC)) {
            $collection['element_' . $row['id']] = [
                'id' => $row['id'],
                'downloaded' => 0,
                'parent' => $row['parent']];
        }
        
        // Get the downloaded information
        $get_downloads_in_period  = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'" . PHP_EOL;
        $get_downloads_in_period .= "FROM download d" . PHP_EOL;
        $get_downloads_in_period .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
        $get_downloads_in_period .= CourseDownloadsController::$timeIntervals[$interval_index] . PHP_EOL;
        $get_downloads_in_period .= "GROUP BY d.file" . PHP_EOL;
        $get_downloads_in_period .= "HAVING COUNT(d.id) > 0" . PHP_EOL;
        $get_downloads_in_period .= "ORDER BY downloaded_times DESC, a.added DESC" . PHP_EOL;
        
        $get_downloads_in_period_query = Database::$db->prepare($get_downloads_in_period);
        $get_downloads_in_period_query->execute();
        while ($row = $get_downloads_in_period_query->fetch(\PDO::FETCH_ASSOC)) {
            // Apply the dowloaded information to the collection array
            if (isset($collection['element_' . $row['id']])) {
                $collection['element_' . $row['id']]['downloaded'] = $row['downloaded_times'];
            }
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