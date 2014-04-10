<?php
/*
 * File: collection.php
 * Holds: Holds the collection of all the items currently loaded (to reduce queries)
 * Created: 09.04.14
 * Last updated: 05.12.13
 * Project: Youkok2
 * 
*/

//
// Collection of Items. Made to reduce loading and make caching easier
//

class Collection {
    
    //
    // Variables
    //
    
    private $arr;
    
    //
    // Constructor
    //
    
    public function _construct() {
        $arr = array();
    }
    
    //
    // Add Item to collection without validating duplicates
    //
    
    public function add($elm) {
        $arr[$elm->getId()] = $elm;
    }

    //
    //
    //

    public function addIfDoesNotExist($elm) {
        // Get element id
        $elm_id = $elm->getId();

        // Foreach and check if found
        foreach ($arr as $k => $v) {
            if ($k == $elm_id) {
                return;
            }
        }

        // Not found, add
        $arr[$elm_id] = $elm;
    }
    
    //
    // Return an item
    //

    public function get($id) {
        // Check if found
        if (!isset($arr[$id])) {
            // Not found
            return null;
        }
        else {
            // Return object
            return $arr[$id];
        }
    }
}
?>