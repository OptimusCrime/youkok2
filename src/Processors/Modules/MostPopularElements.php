<?php
/*
 * File: MostPopularElements.php
 * Holds: Change module settings
 * Created: 11.01.2015
 * Project: Youkok2
 * 
*/

namespace Youkok2\Processors\Modules;

use \Youkok2\Models\Element as Element;
use \Youkok2\Models\Me as Me;
use \Youkok2\Models\Controllers\ElementController as ElementController;
use \Youkok2\Utilities\Database as Database;

class MostPopularElements extends ModuleProcessor {
    
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
        // Get the correct delta
         if ($this->getSetting('module1_delta') !== null and !is_array($this->getSetting('module1_delta'))) {
            $delta_numeric = $this->getSetting('module1_delta');
        }
        else {
            $delta_numeric = Me::getModuleSettings('module1_delta');
        }
        
        // Make sure we have a delta
        if ($delta_numeric == null or $delta_numeric < 0 or $delta_numeric > 4) {
            $delta_numeric = 3;
        }
        
        // Load most popular files from the system
        $get_most_popular  = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'" . PHP_EOL;
        $get_most_popular .= "FROM download d" . PHP_EOL;
        $get_most_popular .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
        $get_most_popular .= ElementController::$timeIntervals[$delta_numeric] . PHP_EOL;
        $get_most_popular .= "GROUP BY d.file" . PHP_EOL;
        $get_most_popular .= "HAVING COUNT(d.id) > 0" . PHP_EOL;
        $get_most_popular .= "ORDER BY downloaded_times DESC, a.added DESC" . PHP_EOL;
        $get_most_popular .= "LIMIT 15";
        
        $get_most_popular_query = Database::$db->prepare($get_most_popular);
        $get_most_popular_query->execute();
        while ($row = $get_most_popular_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get
            $element = Element::get($row['id']);
            $element->setDownloadCount($delta_numeric, $row['downloaded_times']);
            $collection[] = $element;
        }
        
        // Set the data
        $this->setData('data', $collection);
    }
    
    /*
     * Update the module
     */
    
    public function update() {
        // Get the correct delta
        $delta_numeric = $this->getSetting('module1_delta');
        
        // Quality check here
        if ($delta_numeric < 0 or $delta_numeric > 4) {
            $delta_numeric = 3;
        }
        
        // Set the new delta
        Me::setModuleSettings('module1_delta', $delta_numeric);
        
        // Check if we should update user preferences
        if (Me::isLoggedIn()) {
            // Update user
            Me::update();
        }
        
        // Run the get method
        $this->get();
    }
}