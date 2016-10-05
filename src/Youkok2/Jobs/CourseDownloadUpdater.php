<?php
namespace Youkok2\Jobs;

use Youkok2\Models\Element;
use Youkok2\Models\Cache\CourseDownloads;
use Youkok2\Models\Controllers\Cache\CourseDownloadsController;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Database;
use Youkok2\Youkok2;

class CourseDownloadUpdater extends Youkok2 implements BaseJob
{
    
    public function run() {
        for ($i = 0; $i <= 4; $i++) {
            $this->fetchCourseDownloads($i);
        }
    }
    
    public function done() {
        // Force cache manager to store all cache
        CacheManager::store();
    }

    private function fetchCourseDownloads($interval_index) {
        $collection = [];
        
        $get_downloads_in_period  = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'" . PHP_EOL;
        $get_downloads_in_period .= "FROM download d" . PHP_EOL;
        $get_downloads_in_period .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
        $get_downloads_in_period .= CourseDownloadsController::$timeIntervals[DATABASE_ADAPTER][$interval_index] .
            PHP_EOL;
        $get_downloads_in_period .= "GROUP BY d.file" . PHP_EOL;
        $get_downloads_in_period .= "HAVING COUNT(d.id) > 0" . PHP_EOL;
        $get_downloads_in_period .= "ORDER BY downloaded_times DESC, a.added DESC" . PHP_EOL;
        
        $get_downloads_in_period_query = Database::$db->prepare($get_downloads_in_period);
        $get_downloads_in_period_query->execute();
        while ($row = $get_downloads_in_period_query->fetch(\PDO::FETCH_ASSOC)) {
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
        
        arsort($collection);
        
        // Get the 15 best results (if any)
        $result_array = [];
        $i = 0;
        foreach ($collection as $k => $v) {
            $result_array[] = [
                'id' => $k,
                'downloaded' => $v
            ];
            
            $i++;
            
            if ($i == 15) {
                break;
            }
        }
        
        $course_downloads = new CourseDownloads();
        $course_downloads->setId($interval_index);
        $course_downloads->setData(json_encode($result_array));
        
        // Cache the element
        $course_downloads->save();
    }
}
