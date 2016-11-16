<?php
namespace Youkok2\Models\StaticControllers\Cache;

use Youkok2\Youkok2;
use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Cache\MeDownloads;
use Youkok2\Models\Controllers\Cache\MeDownloadsController;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Database;

class MeDownloadsStaticController
{

    public static function get($me) {
        $cache = [];
        if (CacheManager::isCached($me->getId(), MeDownloadsController::$cacheKey)) {
            $cache_data = CacheManager::getCache($me->getId(), MeDownloadsController::$cacheKey);
            
            if ($cache_data !== null and is_array($cache_data) and isset($cache_data['data'])) {
                $cache_data_decoded = json_decode($cache_data['data'], true);
                if (is_array($cache_data_decoded)) {
                    $cache = $cache_data_decoded;
                }
            }
        }
        else {
            $elements = [];
        
            $get_last_downloads  = "SELECT d.file" . PHP_EOL;
            $get_last_downloads .= "FROM download AS d" . PHP_EOL;
            $get_last_downloads .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
            $get_last_downloads .= "WHERE d.user = :user" . PHP_EOL;
            $get_last_downloads .= "AND a.pending = 0" . PHP_EOL;
            $get_last_downloads .= "AND a.deleted = 0" . PHP_EOL;
            $get_last_downloads .= "ORDER BY d.downloaded_time DESC" . PHP_EOL;
            $get_last_downloads .= "LIMIT 15";
            
            $get_last_downloads_query = Database::$db->prepare($get_last_downloads);
            $get_last_downloads_query->execute([':user' => $me->getId()]);
            while ($row = $get_last_downloads_query->fetch(\PDO::FETCH_ASSOC)) {
                $elements[] = $row['file'];
            }
            
            $me_downloads = new MeDownloads();
            $me_downloads->setId($me->getId());
            $me_downloads->setData(json_encode($elements));
            $me_downloads->cache(true);
            
            $cache = $elements;
        }
        
        return self::processCache($cache);
    }
    
    private static function processCache($cache) {
        if (count($cache) > 0) {
            $elements = [];
        
            foreach ($cache as $v) {
                $element = Element::get($v);
                $elements[] = $element;
            }
            
            return $elements;
        }
        
        return [];
    }
}
