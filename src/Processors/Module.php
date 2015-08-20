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
     * Override
     */

    protected function requireDatabase() {
        return true;
    }

    /*
     * Override
     */

    protected function encodeData($data) {
        $new_data = [];

        // Loop the data array and run method on each element
        if (count($data['data']) > 0) {
            foreach($data['data'] as $v) {
                $new_data[] = $v->toArray();
            }
        }

        // Set new value
        $data['data'] = $new_data;

        // Return the updated array
        return $data;
    }

    /*
     * Constructor
     */

    public function __construct($method, $settings) {
        // Calling Base' constructor
        parent::__construct($method, $settings);
    }
    
    /*
     * Fetch module data
     */
    
    public function get() {
        // For returning content
        $collection = [];
        
        // Get the correct delta
        if ($this->getSettings('delta') !== null) {
            $delta_numeric = $this->getSettings('delta');
        }
        else {
            $delta_numeric = Me::getMostPopularDelta();
        }
        
        // Load most popular files from the system
        $get_most_popular  = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'" . PHP_EOL;
        $get_most_popular .= "FROM download d" . PHP_EOL;
        $get_most_popular .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
        $get_most_popular .= ElementController::$delta[$delta_numeric] . PHP_EOL;
        $get_most_popular .= "GROUP BY d.file" . PHP_EOL;
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