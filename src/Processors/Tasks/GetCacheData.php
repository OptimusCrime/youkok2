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

    public function __construct($outputData = false, $returnData = false) {
        // Calling Base' constructor
        parent::__construct($outputData, $returnData);
        
        if (self::requireCli() or self::requireAdmin()) {
            // Check database
            if ($this->makeDatabaseConnection()) {
                // Get the data
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
        
        if ($this->outputData) {
            $this->outputData();
        }
        if ($this->returnData) {
            $this->returnData();
        }
    }

    /*
     * Fetch the actual data here
     */

    private function getCacheData() {
        if (isset($_GET['id']) and is_numeric($_GET['id'])) {
            $element = new Element();
            
            // Add pre data
            $this->setData('pre', [
                'id' => $element->getId(),
                'name' => $element->getName(),
                'url_friendly' => $element->getUrlFriendly(),
                'owner' => $element->getOwner(),
                'parent' => $element->getParent(),
                'empty' => $element->isEmpty(),
                'checksum' => $element->getChecksum(),
                'mime_type' => $element->getMimeType(),
                'missing_image' => $element->getMissingImage(),
                'size' => $element->getSize(),
                'directory' => $element->isDirectory(),
                'link' => $element->isLink(),
                'file' => $element->isFile(),
                'accepted' => $element->isAccepted(),
                'visible' => $element->isVisible(),
                'exam' => $element->getExam(),
                'url' => $element->getUrl(),
                'added' => $element->getAdded()
            ]);
            
            // Create element
            $element->createById($_GET['id']);
            
            if ($element->controller->wasFound()) {
                $cache = CacheManager::getCache($element->getId(), 'i');
                
                if ($cache === null) {
                    $this->setData('code', 300);
                    $this->setData('msg', 'Not cached');
                }
                else {
                    $this->setData('code', 200);
                    $this->setData('cache', $cache);
                }
            }
            
            // Add cache format
            $this->setData('cache_format', $element->controller->cacheFormat());
            
            // Add data
            $this->setData('data', [
                'id' => $element->getId(),
                'name' => $element->getName(),
                'url_friendly' => $element->getUrlFriendly(),
                'owner' => $element->getOwner(),
                'parent' => $element->getParent(),
                'empty' => $element->isEmpty(),
                'checksum' => $element->getChecksum(),
                'mime_type' => $element->getMimeType(),
                'missing_image' => $element->getMissingImage(),
                'size' => $element->getSize(),
                'directory' => $element->isDirectory(),
                'link' => $element->isLink(),
                'file' => $element->isFile(),
                'accepted' => $element->isAccepted(),
                'visible' => $element->isVisible(),
                'exam' => $element->getExam(),
                'url' => $element->getUrl(),
                'added' => $element->getAdded()
            ]);
        }
        else {
            // Missing id
            $this->setData('code', 500);
            $this->setData('msg', 'No id');
        }
    }
} 