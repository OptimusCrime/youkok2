<?php
/*
 * File: CourseDownloadUpdater.php
 * Holds: Job that updates the most popular downloads
 * Created: 29.11.2015
 * Project: Youkok2
*/

namespace Youkok2\Jobs;

use Youkok2\Models\Element;
use Youkok2\Models\Cache\CourseDownloads;
use Youkok2\Models\Controllers\Cache\CourseDownloadsController;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Database;
use Youkok2\Youkok2;

class CourseDownloadUpdater extends Youkok2 
{
    
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
            $element = new Element($row['id']);
            $parent = $element->getRootParent();
            
            if ($parent->wasFound()) {
                if (isset($collection[$parent->getId()])) {
                    $collection[$parent->getId()] += $row['downloaded_times'];
                }
                else {
                    $collection[$parent->getId()] = $row['downloaded_times'];
                }
            }
        }
        
        // Sort the result
        arsort($collection);
        
        // Get the 15 best results (if any)
        $result_array = [];
        $i = 0;
        foreach ($collection as $k => $v) {
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
