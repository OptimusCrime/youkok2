<?php
/*
 * File: ClearCache.php
 * Holds: Clears all the cache (Smarty and Youkok2)
 * Created: 17.12.14
 * Project: Youkok2
*/

namespace Youkok2\Processors\Tasks;

/*
 * Define what classes to use
 */

use \Youkok2\Processors\BaseProcessor as BaseProcessor;

/*
 * ClearCache extending Base
 */

Class ClearCache extends BaseProcessor {

    /*
     * Construct
     */

    public function __construct($outputData = false, $returnData = false) {
        // Calling Base' constructor
        parent::__construct($outputData, $returnData);
        
        // Check access (only cli and admin)
        if (self::requireCli() or self::requireAdmin()) {
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
            $smarty->setCacheDir(CACHE_PATH . '/smarty/');

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
            
            foreach (array_filter(glob(CACHE_PATH . '/elements/*'), 'is_dir') as $v) {
                $this->rmNonemptyDir($v);
            }
            
            // Set data
            $this->setData('code', 200);
            $this->setData('msg', 'Cache cleared');
        }
        else {
            // No access
            $this->noAccess();
        }
        
        // Return data
        if ($this->returnData) {
            $this->returnData();
        }
        if ($this->outputData) {
            $this->outputData();
        }
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