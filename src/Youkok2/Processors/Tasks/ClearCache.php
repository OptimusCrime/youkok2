<?php
/*
 * File: ClearCache.php
 * Holds: Clears all the cache (Smarty and Youkok2)
 * Created: 17.12.14
 * Project: Youkok2
 *
 */

namespace Youkok2\Processors\Tasks;

use Youkok2\Processors\BaseProcessor;

Class ClearCache extends BaseProcessor {
    
    /*
     * Override
     */

    protected function checkPermissions() {
        return $this->requireCli() or $this->requireAdmin();
    }

    /*
     * Construct
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
    }

        
    /*
     * Run method
     */

    protected function run() {
        /*
         * Typeahead
         */

        if (file_exists(CACHE_PATH . '/courses.json')) {
            copy(CACHE_PATH . '/courses.json', BASE_PATH . '/courses.json');
        }
        if (file_exists(CACHE_PATH . '/typeahead.json')) {
            copy(CACHE_PATH . '/typeahead.json', BASE_PATH . '/typeahead.json');
        }

        /*
         * Smarty
         */

        // New Smarty instance
        $smarty = new \Smarty();
        $smarty->setCompileDir(CACHE_PATH . '/smarty/compiled/');
        $smarty->setCacheDir(CACHE_PATH . '/smarty/cache/');

        // Clear cache and compiled templates
        $smarty->clearAllCache();
        $smarty->clearCompiledTemplate();

        /*
         * Restore Typeahead
         */

        // Copy
        if (file_exists(BASE_PATH . '/courses.json')) {
            copy(BASE_PATH . '/courses.json', CACHE_PATH . '/courses.json');
            unlink(BASE_PATH . '/courses.json');
        }
        if (file_exists(BASE_PATH . '/typeahead.json')) {
            copy(BASE_PATH . '/typeahead.json', CACHE_PATH . '/typeahead.json');
            unlink(BASE_PATH . '/typeahead.json');
        }

        /*
         * Youkok2 cache
         */

        foreach (array_filter(glob(CACHE_PATH . '/youkok/*'), 'is_dir') as $v) {
            $this->rmNonemptyDir($v);
        }

        // Set data
        $this->setData('code', 200);
        $this->setData('msg', 'Cache cleared');
    }
    
    private function rmNonemptyDir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype($dir . '/' . $object) == 'dir') {
                        $this->rmNonemptyDir($dir . '/' . $object);
                    }
                    else {
                        unlink($dir . '/' . $object);
                    }
                }
            }
            
            reset($objects);
            rmdir($dir);
        }
    }
}