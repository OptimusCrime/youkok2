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

use \Youkok2\Processors\Base as Base;

/*
 * ClearCache extending Base
 */

Class ClearCache extends Base {

    /*
     * Construct
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
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
            }
            if (file_exists(BASE_PATH . '/typeahead.json')) {
                copy(BASE_PATH . '/typeahead.json', CACHE_PATH . '/typeahead.json');
            }
            
            // Delete
            unlink(BASE_PATH . '/courses.json');
            unlink(BASE_PATH . '/typeahead.json');
            
            // Set data
            $this->setData('code', 200);
            $this->setData('msg', 'Cache cleared');
        }
        else {
            // No access
            $this->noAccess();
        }
        
        // Return data
        $this->returnData();
    }
}