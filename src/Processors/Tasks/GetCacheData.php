<?php
/*
 * File: GetCacheData.php
 * Holds: Returns the actual cache data for a file
 * Created: 15.05.2015
 * Project: Youkok2
*/

namespace Youkok2\Processors\Tasks;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Element as Element;
use \Youkok2\Processors\Base as Base;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\CacheManager as CacheManager;

/*
 * LoadExams extending Base
 */

class GetCacheData extends Base {
    
    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
        if (self::requireCli() or self::requireAdmin()) {
            // Check database
            if ($this->checkDatabase()) {
                $this->getCacheData();
                
                // Close database connection
                Database::close();
            }
            else {
                $this->setError();
            }
        }
        else {
            // No access
            $this->noAccess();
        }
        
        // Return data
        $this->returnData();
    }

    /*
     * Check if we can connect to the database
     */

    private function checkDatabase() {
        try {
            Database::connect();

            return true;
        }
        catch (Exception $e) {
            $this->setData('code', 500);
            $this->setData('msg', 'Could not connect to database');

            return false;
        }
    }
    
    /*
     * Reset exam information here
     */
    
    private function resetExamData() {
        $reset_exam  = "UPDATE archive" . PHP_EOL;
        $reset_exam .= "SET exam = NULL" . PHP_EOL;
        
        Database::$db->query($reset_exam);
    }

    /*
     * Fetch the actual data here
     */

    private function getCacheData() {
        if (isset($_GET['id']) and is_numeric($_GET['id'])) {
            $element = new Element();
            $element->createById($_GET['id']);
            
            if ($element->controller->wasFound()) {
                $cache = CacheManager::getCache($element->getId(), 'i');
                $this->setData('data', $cache);
                var_dump($element);
            }
        }
        else {
            $this->setData('code', 200);
        }
    }
} 