<?php
/*
 * File: ElementCollection.php
 * Holds: Holds the collection of all the items currently loaded (to reduce queries)
 * Created: 09.04.14
 * Project: Youkok2
*/

namespace Youkok2\Collections;

/*
 * Define what classes to use
 */

use Youkok2\Models\Element;

/*
 * The class ElementCollection, called statically
 */

class ElementCollection {
    
    /*
     * Variables
     */
    
    private static $arr = array();
    private static $isInited = false;
    
    /*
     * Add element to the collection
     */
    
    public static function add($elm) {
        // Check if should init self
        if (!self::$isInited) {
            self::addInitial();
        }
        
        // Add element to self
        self::$arr[$elm->getId()] = $elm;
    }
    
    /*
     * Return an element, or null, if not found
     */

    public static function get($id) {
        // Check if found
        if (!isset(self::$arr[$id])) {
            // Not found
            return null;
        }
        else {
            // Return object
            return self::$arr[$id];
        }
    }
    
    /*
     * Init the list by adding the root element
     */
    
    private static function addInitial() {
        // Reset variable
        self::$isInited = true;
        
        // Add root element to collection
        $root_element = new Element();
        $root_element->createById(1);
        self::add($root_element);
    }
    
    /*
     * Return size of the collection
     */
    
    public static function getSize() {
        return count(self::$arr);
    }
}