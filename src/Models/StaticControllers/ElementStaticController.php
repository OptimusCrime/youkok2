<?php
/*
 * File: ElementStaticController.php
 * Holds: Holds methods for the static Element class
 * Created: 26.05.2015
 * Project: Youkok2
*/

namespace Youkok2\models\StaticControllers;

/*
 * Loads other classes
 */

use \Youkok2\Youkok2 as Youkok2;
use \Youkok2\Collections\ElementCollection as ElementCollection;
use \Youkok2\Models\Element as Element;
use \Youkok2\Models\Me as Me;
use \Youkok2\Utilities\Database as Database;
use \Youkok2\Utilities\Routes as Routes;
use \Youkok2\Utilities\Utilities as Utilities;

/*
 * The Course class
 */

class ElementStaticController {
    
    /*
     * Fetch newest Elements
     */
    
    public static function getNewest() {
        // For storing the collection
        $collection = '';
        
        // Loading newest files from the system
        $get_newest  = "SELECT id" . PHP_EOL;
        $get_newest .= "FROM archive" . PHP_EOL;
        $get_newest .= "WHERE is_directory = 0" . PHP_EOL;
        $get_newest .= "AND is_visible = 1" . PHP_EOL;
        $get_newest .= "ORDER BY added DESC, name DESC" . PHP_EOL;
        $get_newest .= "LIMIT 15";
        
        $get_newest_query = Database::$db->query($get_newest);
        while ($row = $get_newest_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get
            $element = ElementCollection::get($row['id'], array('root'));

            // Check if valid Element
            if ($element->wasFound()) {
                $collection[] = $element;
            }
        }
        
        // Return the content
        return $collection;
    }

    /*
     * Fetch most popular Elements
     */

    public static function getMostPopular($override = null) {
        return Youkok2::runProcessor('/module/get', false, true)['html'];
    }
    
    /*
     * Fetch Me favorites
     */
    
    public static function getFavorites() {
        $ret = '';
        $favorites = Me::getFavorites();

        // Check if user has any favorites at all
        if (count($favorites) > 0) {

            foreach ($favorites as $favorite) {
                // Get
                $element = ElementCollection::get($favorite);

                // Check if valid Element
                if ($element !== null) {
                    $ret .= $element->controller->getFrontpageLink('favorites');
                }
            }
        }

        // Check if anything was found
        if (count($favorites) == 0 or $ret == '') {
            $ret = '<li class="list-group-item"><em>Du har ingen favoritter</em></li>';
        }

        // Return the list
        return $ret;
    }
}