<?php
/*
 * File: MeDownloadsStaticController.php
 * Holds: Holds methods for the static MeDownloads class
 * Created: 12.12.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\StaticControllers\Cache;

use Youkok2\Youkok2;
use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Cache\MeDownloads;
use Youkok2\Models\Controllers\Cache\MeDownloadsController;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Database;

class MeDownloadsStaticController {

    /*
     * Variables
     */

    public static function get() {
        // Check if we have a cached version
        $cache = [];
        if (CacheManager::isCached(Me::getId(), MeDownloadsController::$cacheKey)) {
            // We have cache, simply fetch it
            $cache_data = CacheManager::getCache(Me::getId(), MeDownloadsController::$cacheKey);
            
            if ($cache_data !== null and is_array($cache_data) and isset($cache_data['data'])) {
                $cache_data_decoded = json_decode($cache_data['data'], true);
                if (is_array($cache_data_decoded)) {
                    $cache = $cache_data_decoded;
                }
            }
        }
        else {
            // We do not have any cache, fetch the entire set
            $elements = [];
        
            // Load all favorites
            $get_last_downloads  = "SELECT d.file" . PHP_EOL;
            $get_last_downloads .= "FROM download AS d" . PHP_EOL;
            $get_last_downloads .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
            $get_last_downloads .= "WHERE d.user = :user" . PHP_EOL;
            $get_last_downloads .= "AND a.is_visible = 1" . PHP_EOL;
            $get_last_downloads .= "AND d.id = (" . PHP_EOL;
            $get_last_downloads .= "    SELECT dd.id" . PHP_EOL;
            $get_last_downloads .= "    FROM download dd" . PHP_EOL;
            $get_last_downloads .= "    WHERE d.file = dd.file" . PHP_EOL;
            $get_last_downloads .= "    ORDER BY dd.downloaded_time" . PHP_EOL;
            $get_last_downloads .= "    DESC LIMIT 1)" . PHP_EOL;
            $get_last_downloads .= "ORDER BY d.downloaded_time DESC" . PHP_EOL;
            $get_last_downloads .= "LIMIT 15";
            
            $get_last_downloads_query = Database::$db->prepare($get_last_downloads);
            $get_last_downloads_query->execute(array(':user' => Me::getId()));
            while ($row = $get_last_downloads_query->fetch(\PDO::FETCH_ASSOC)) {
                $elements[] = $row['file'];
            }
            
            // Cache the results
            $me_downloads = new MeDownloads();
            $me_downloads->setId(Me::getId());
            $me_downloads->setData(json_encode($elements));
            $me_downloads->cache(true);
            
            $cache = $elements;
        }
        
        // Process the cache
        return self::processCache($cache);
    }
    
    /*
     * Process the cache elements
     */
    
    private static function processCache($cache) {
        // Make sure we have valid cache with data in it
        if (count($cache) > 0) {
            $elements = [];
        
            // Loop the data and create the objects
            foreach ($cache as $v) {
                $element = Element::get($v);
                $elements[] = $element;
            }
            
            // Return the element collection
            return $elements;
        }
        
        // No cache or no results
        return [];
    }
}