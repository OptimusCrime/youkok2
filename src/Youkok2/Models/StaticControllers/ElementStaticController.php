<?php
/*
 * File: ElementStaticController.php
 * Holds: Holds methods for the static Element class
 * Created: 26.05.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\StaticControllers;

use Youkok2\Youkok2;
use Youkok2\Models\Element;
use Youkok2\Utilities\Database;

class ElementStaticController {

    /*
     * Variables
     */

    private static $collection = [];

    /*
     * Return new Element object
     */

    public static function get($data = null) {
        // Check what kind of fetch we should do
        if (is_numeric($data)) {
            // Check if we have to create it first
            if (!isset(self::$collection[$data])) {
                // Not fetched, new instance
                $element = new Element($data);

                // Add to collection
                self::$collection[$data] = &$element;
            }

            // Return from collection
            return self::$collection[$data];
        }
        else {
            // Fetching by URL, we can't use the collection here, or data is null. Either way...
            return new Element($data);
        }
    }


    /*
     * Fetch newest Elements
     */
    
    public static function getNewest($limit = 15) {
        // For storing the collection
        $collection = '';
        
        // Loading newest files from the system
        $get_newest  = "SELECT id" . PHP_EOL;
        $get_newest .= "FROM archive" . PHP_EOL;
        $get_newest .= "WHERE is_directory = 0" . PHP_EOL;
        $get_newest .= "AND is_visible = 1" . PHP_EOL;
        $get_newest .= "ORDER BY added DESC, name DESC" . PHP_EOL;
        $get_newest .= "LIMIT " . $limit;
        
        $get_newest_query = Database::$db->query($get_newest);
        while ($row = $get_newest_query->fetch(\PDO::FETCH_ASSOC)) {
            // Get
            $collection[] = Element::get($row['id']);
        }
        
        // Return the content
        return $collection;
    }
}