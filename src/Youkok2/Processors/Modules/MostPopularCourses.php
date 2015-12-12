<?php
/*
 * File: MostPopularCourses.php
 * Holds: Change module settings
 * Created: 11.01.2015
 * Project: Youkok2
 * 
*/

namespace Youkok2\Processors\Modules;

use Youkok2\Models\Element;
use Youkok2\Models\Me;
use Youkok2\Models\Cache\CourseDownloads;
use Youkok2\Models\Controllers\CourseDownloadsController;
use Youkok2\Utilities\CacheManager;
use Youkok2\Utilities\Database;

class MostPopularCourses extends ModuleProcessor {
    
    /*
     * Constructor
     */
    
    public function __construct($method, $settings) {
        parent::__construct($method, $settings);
    }
    
    /*
     * Get the module
     */
    
    public function get() {
        if ($this->getSetting('module2_delta') !== null and !is_array($this->getSetting('module2_delta'))) {
            $delta_numeric = $this->getSetting('module2_delta');
        }
        else {
            $delta_numeric = Me::getModuleSettings('module2_delta');
        }
        
        // Make sure we have a delta
        if ($delta_numeric == null or $delta_numeric < 0 or $delta_numeric > 4) {
            $delta_numeric = 3;
        }
        
        // Get the cache from the cachemanager
        $course_downloads = CacheManager::getCache($delta_numeric, 'cd');
        
        // Make sure the data is valid
        $collection = [];
        if (isset($course_downloads['data']) and strlen($course_downloads['data']) > 0) {
            $course_downloads_clean = json_decode($course_downloads['data'], true);
            
            // Make sure the data was valid json
            if (is_array($course_downloads_clean) and count($course_downloads_clean) > 0) {
                foreach ($course_downloads_clean as $v) {
                    $element = Element::get($v['id']);
                    $element->setDownloadCount($delta_numeric, $v['downloaded']);
                    $collection[] = $element;
                }
            }
        }
        
        // Set the data
        $this->setData('data', $collection);
    }
    
    /*
     * Update the module
     */
    
    public function update() {
        // Get the correct delta
        $delta_numeric = $this->getSetting('module2_delta');
        
        // Quality check here
        if ($delta_numeric < 0 or $delta_numeric > 4) {
            $delta_numeric = 3;
        }
        
        // Set the new delta
        Me::setModuleSettings('module2_delta', $delta_numeric);
        
        // Check if we should update user preferences
        if (Me::isLoggedIn()) {
            // Update user
            Me::update();
        }
        
        // Run the get method
        $this->get();
    }
}
