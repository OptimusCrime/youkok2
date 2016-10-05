<?php
namespace Youkok2\Utilities;

class CacheManager
{
    
    private static $cacheArr = [];
    private static $currentChecking = null;
    private static $currentContent = null;
    private static $count = 0;
    private static $bactrace = [];
    private static $profilingDuration = 0;

    public static function isCached($id, $type) {
        $ret = false;
        
        if (defined('PROFILING') and PROFILING) {
            $start_time = microtime(true);
        }
        
        $file = self::getFileName($id, $type);

        if (file_exists($file)) {
            $temp_content = file_get_contents($file);
            
            self::$count++;
            
            if (DEV) {
                self::$bactrace[] = ['id' => $id,
                    'type' => $type,
                    'backtrace' => debug_backtrace()];
            }

            // Check if content is valid (and safe!)
            if (substr(file_get_contents($file), 0, 13) == '<?php return ') {
                self::$currentChecking = $id;
                self::$currentContent = $temp_content;

                $ret = true;
            }
            else {
                self::deleteCache($id, $type);
            }
        }
        else {
            self::$currentChecking = null;
            self::$currentContent = null;
        }
        
        if (defined('PROFILING') and PROFILING) {
            self::$profilingDuration += microtime(true) - $start_time;
        }

        return $ret;
    }

    public static function getCache($id, $type) {
        $content = null;
        
        if (defined('PROFILING') and PROFILING) {
            $start_time = microtime(true);
        }
        
        if (self::$currentChecking == $id) {
            $content = self::evalAndClean(self::$currentContent);
        }
        else {
            if (self::isCached($id, $type)) {
                $content = self::evalAndClean(self::$currentContent);
            }
        }
        
        if (defined('PROFILING') and PROFILING) {
            self::$profilingDuration += microtime(true) - $start_time;
        }
        
        return $content;
    }

    public static function setCache($id, $type, $content, $force = false) {
        if (!is_array($content)) {
            return false;
        }

        $file = self::getFileName($id, $type);

        $data = '<?php return "' . addslashes(json_encode($content)) . '"; ?>';

        if ($force) {
            $parent_dir = CACHE_PATH . '/youkok/' . $type;
            if (!file_exists($parent_dir)) {
                mkdir($parent_dir);
            }
            
            $hash = self::getHash($id, $type);
            $parent_dir .= '/' . substr($hash, 0, 1);
            if (!file_exists($parent_dir)) {
                mkdir($parent_dir);
            }
            
            $parent_dir .= '/' . substr($hash, 1, 1);
            if (!file_exists($parent_dir)) {
                mkdir($parent_dir);
            }

            file_put_contents($file, $data);
        }
        else {
            self::$cacheArr[$id . '-' . $type] = ['id' => $id,
                'type' => $type,
                'content' => $content];
        }

        return true;
    }

    public static function deleteCache($id, $type) {
        $file = self::getFileName($id, $type);

        @unlink($file);
    }

    public static function store() {
        if (defined('PROFILING') and PROFILING) {
            $start_time = microtime(true);
        }
        
        if (count(self::$cacheArr) > 0) {
            foreach (self::$cacheArr as $k => $v) {
                self::setCache($v['id'], $v['type'], $v['content'], true);
            }

            self::$cacheArr = [];
        }
        
        if (defined('PROFILING') and PROFILING) {
            self::$profilingDuration += microtime(true) - $start_time;
        }
    }

    private static function evalAndClean($c) {
        return json_decode(eval(str_replace(['<?php', '?>'], '', $c)), true);
    }

    private static function getFileName($id, $type) {
        $hash = self::getHash($id, $type);
        return CACHE_PATH . '/youkok/' . $type . '/' . substr($hash, 0, 1) . '/' .
            substr($hash, 1, 1) . '/' . $hash . '_c.php';
    }

    private static function getHash($id, $type) {
        return substr(md5('lorem ' . $type . ' ipsum' . $id . md5($id)), 0, 22);
    }
    
    public static function loadTypeaheadCache() {
        if (file_exists(CACHE_PATH . '/typeahead.json')) {
            $content = json_decode(file_get_contents(CACHE_PATH . '/typeahead.json'), true);
            
            if (!isset($content['timestamp'])) {
                return time();
            }
            else {
                return $content['timestamp'];
            }
        }
        else {
            return time();
        }
    }
    
    public static function getCount() {
        return self::$count;
    }
    
    public static function getProfilingDuration() {
        return round(self::$profilingDuration, 4);
    }
}
