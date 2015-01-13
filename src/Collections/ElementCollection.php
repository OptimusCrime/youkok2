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
        // Add element to self
        self::$arr[$elm->getId()] = $elm;
    }
    
    /*
     * Return an element, or null, if not found
     */

    public static function get($id, $flags = null) {
        // Check if already fetched
        if (!isset(self::$arr[$id])) {
            // Not fetched, try to load
            $element = new Element();
            
            // Check if we should set flags
            if ($flags != null) {
                // TODO
            }
            
            // Create element
            $element->createById($id);
            
            // Check if it was found
            if ($element->controller->wasFound()) {
                // Add to list
                self::add($element);

                // Return element
                return $element;
            }
            
            // Not found
            return null;
        }
        else {
            // Return object
            return self::$arr[$id];
        }
    }
    
    /*
     * Checks is a element is in the collection
     */
    
    public static function isStored($id) {
        if (count(self::$arr) > 0) {
            foreach (self::$arr as $v) {
                if ($id == $v->getId()) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /*
     * Return size of the collection
     */
    
    public static function getSize() {
        return count(self::$arr);
    }
}