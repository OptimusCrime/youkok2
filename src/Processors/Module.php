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

use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Models\Me as Me;
use \Youkok2\Models\Controllers\ElementController as ElementController;
use \Youkok2\Utilities\Database as Database;

/*
 * The NotFound class, extending Base class
 */

class Module extends Base {
    
    /*
     * Constructor
     */

    public function __construct($returnData = false) {
        // Calling Base' constructor
        parent::__construct($returnData);
        
        // Check database
        if ($this->makeDatabaseConnection()) {
            // TODO
            $this->update();
            
            // Close database connection
            if (!$returnData) {
                Database::close();
            }
        }
        
        // Return data
        $this->returnData();
    }
    
    /*
     * TODO
     */
    
    public function update() {
        $ret = '';
        
        // Get correct delta
        if (!isset($_POST['delta'])) {
            $user_delta = Me::getMostPopularDelta();
        }
        else {
            $user_delta = $_POST['delta'];
        }

        // Load most popular files from the system
        $get_most_popular  = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'" . PHP_EOL;
        $get_most_popular .= "FROM download d" . PHP_EOL;
        $get_most_popular .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
        $get_most_popular .= ElementController::$delta[$user_delta] . PHP_EOL;
        $get_most_popular .= "GROUP BY d.file" . PHP_EOL;
        $get_most_popular .= "ORDER BY downloaded_times DESC, a.added DESC" . PHP_EOL;
        $get_most_popular .= "LIMIT 15";

        $get_most_popular_query = Database::$db->prepare($get_most_popular);
        $get_most_popular_query->execute();
        while ($row = $get_most_popular_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get
            $element = ElementCollection::get($row['id'], array('root'));

            // Check if valid Element
            if ($element !== null) {
                // Set download count
                $element->controller->setDownloadCount($user_delta, $row['downloaded_times']);
                
                // Generate string
                $ret .= $element->controller->getFrontpageLink('most-popular', $user_delta);
            }
        }

        // Check if null
        if ($ret == '') {
            $ret = '<li class="list-group-item">Det er visst ingen nedlastninger i dette tidsrommet.</li>';
        }
        
        $this->setData('code', 200);
        $this->setData('html', $ret);
    }
}