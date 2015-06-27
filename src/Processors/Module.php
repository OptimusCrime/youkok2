<?php
/*
 * File: Module.php
 * Holds: Change module settings
 * Created: 11.01.15
 * Project: Youkok2
 * 
*/

namespace Youkok2\Processors;

/*
 * Define what classes to use
 */

use \Youkok2\Models\Element as Element;
use \Youkok2\Models\Me as Me;
use \Youkok2\Models\Controllers\ElementController as ElementController;
use \Youkok2\Utilities\Database as Database;

/*
 * The NotFound class, extending Base class
 */

class Module extends BaseProcessor {
    
    /*
     * Constructor
     */

    public function __construct($method, $noOutput = false) {
        // Calling Base' constructor
        parent::__construct($method, $noOutput);
    }
    
    /*
     * Fetch module data
     */
    
    public function get() {
        // For returning content
        $collection = [];
        
        // Load most popular files from the system
        $get_most_popular  = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'" . PHP_EOL;
        $get_most_popular .= "FROM download d" . PHP_EOL;
        $get_most_popular .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
        $get_most_popular .= ElementController::$delta[Me::getMostPopularDelta()] . PHP_EOL;
        $get_most_popular .= "GROUP BY d.file" . PHP_EOL;
        $get_most_popular .= "ORDER BY downloaded_times DESC, a.added DESC" . PHP_EOL;
        $get_most_popular .= "LIMIT 15";

        $get_most_popular_query = Database::$db->prepare($get_most_popular);
        $get_most_popular_query->execute();
        while ($row = $get_most_popular_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get
            $element = Element::get($row['id']);
            $element->setDownloadCount(Me::getMostPopularDelta(), $row['downloaded_times']);
            $collection[] = $element;
        }

        return $collection;
        
        // Set data
        $this->setData('html', $ret);
        $this->setData('code', 200);
        
        // Handle output
        if ($this->outputData) {
            $this->outputData();
        }
        if ($this->returnData) {
            return $this->returnData();
        }
    }
    
    /*
     * Update module information
     */
    
    public function update() {
        $user_delta = 0;
        if (isset($_POST['delta'])) {
            $user_delta = $_POST['delta'];
        }
        
        // Quality check here
        if ($user_delta < 0 or $user_delta > 4) {
            $user_delta = 0;
        }
        
        // Set the new delta
        Me::setMostPopularDelta($user_delta);
        
        // Check if we should update user preferences
        if (Me::isLoggedIn()) {
            // Update user
            Me::update();
        }
        
        return $this->get();
    }
}