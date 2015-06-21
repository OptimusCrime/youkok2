<?php
/*
 * File: CacheManager.php
 * Holds: Manges cache of Item objects
 * Created: 13.06.14
 * Project: Youkok2
 * 
*/

namespace Youkok2\Utilities;

/*
 * The CacheManager class
 */

Class CacheManager {

    /*
     * Internal variables
     */
    
    private static $cacheArr = [];
    private static $currentChecking = null;
    private static $currentContent = null;
    private static $fetches = 0;
    private static $bactrace = array();
    
    /*
     * Check if cached
     */

    public static function isCached($id, $type) {
        // Generate full path for item
        $file = self::getFileName($id, $type);

        // Check if file exists
        if (file_exists($file)) {
            // Get content
            $temp_content = file_get_contents($file);
            
            // Increase fetch
            self::$fetches++;
            
            // Debug
            if (DEV) {
                self::$bactrace[] = array('id' => $id, 
                    'type' => $type,
                    'backtrace' => debug_backtrace());
            }

            // Check if content is valid (and safe!)
            if (substr(file_get_contents($file), 0, 13) == '<?php return ') {
                // Is valid, store current
                self::$currentChecking = $id;
                self::$currentContent = $temp_content;

                // Return true
                return true;
            }
            else {
                // Delete invalid cache
                self::deleteCache($id, $type);
            }
        }
        else {
            // Reset current
            self::$currentChecking = null;
            self::$currentContent = null;

            // Return status
            return false;
        }
    }

    /*
     * Return cache
     */

    public static function getCache($id, $type) {
        // Check if already validated
        if (self::$currentChecking == $id) {
            return self::evalAndClean(self::$currentContent);
        }
        else {
            // Validate first
            if (self::isCached($id, $type)) {
                // Is valid
                return self::evalAndClean(self::$currentContent);
            }
            else {
                // Return null, this is not a valid cache
                return null;
            }
        }
    }

    /*
     * Set cahce
     */

    public static function setCache($id, $type, $content, $force = false) {
        // Get file name
        $file = self::getFileName($id, $type);

        // Build content
        $data = '<?php return "' . addslashes($content) . '"; ?>';

        // Check if we should store to disk at once
        if ($force) {
            // Check if directory exists
            $hash = self::getHash($id, $type);
            $parent_dir = CACHE_PATH . '/elements/' . substr($hash, 0, 1);
            if (!file_exists($parent_dir)) {
                mkdir($parent_dir);
            }
            
            // Second parent dir
            $parent_dir .= '/' . substr($hash, 1, 1);
            if (!file_exists($parent_dir)) {
                mkdir($parent_dir);
            }

            // Store content in file
            file_put_contents($file, $data);
        }
        else {
            // Queue storing for later
            self::$cacheArr[$id . '-' . $type] = ['id' => $id,
                'type' => $type,
                'content' => $content];
        }
    }

    /*
     * Clear cache
     */

    public static function deleteCache($id, $type) {
        // Get file name
        $file = self::getFileName($id, $type);

        // Delete
        @unlink($file);
    }

    /*
     * Store cache to disk
     */

    public static function store() {
        // Check if we got something queued
        if (count(self::$cacheArr) > 0) {
            // Loop all cache items
            foreach (self::$cacheArr as $k => $v) {
                self::setCache($v['id'], $v['type'], $v['content'], true);
            }

            // Clear array
            self::$cacheArr = [];
        }
    }

    /*
     * Private method for evaling and removing php-tags from the file
     */

    private static function evalAndClean($c) {
        return json_decode(eval(str_replace(['<?php', '?>'], '', $c)), true);
    }

    /*
     * Private method for generating hashes used by the cache
     */

    private static function getFileName($id, $type) {
        $hash = self::getHash($id, $type);
        return CACHE_PATH . '/elements/' . substr($hash, 0, 1) . '/' . substr($hash, 1, 1) . '/' . $hash . '_' . $type . '_' . $id . '_c.php';
    }

    /*
     * Private method that returns that hash
     */

    private static function getHash($id, $type) {
        return substr(md5('lorem ' . $type . ' ipsum' . $id . md5($id)), 0, 22);
    }
    
    /*
     * Returns the cache tiemstamp for typeahad
     */
    
    public static function loadTypeaheadCache() {
        if (file_exists(CACHE_PATH . '/typeahead.json')) {
            // File exists
            $content = json_decode(file_get_contents(CACHE_PATH . '/typeahead.json'), true);
            
            // Check if valid content
            if (!isset($content['timestamp'])) {
                // Return the current time
                return time();
            }
            else {
                // Return stored timestamp
                return $content['timestamp'];
            }
        }
        else {
            // Return the current time
            return time();
        }
    }
    
    /*
     * Return number of fetches
     */
    
    public static function getFetches() {
        return self::$fetches;
    }
    
    /*
     * Return the backtrace array
     */
    
    public static function getBacktrace() {
        return self::$bactrace;
    }
}