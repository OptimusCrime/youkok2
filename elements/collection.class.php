<?php
/*
 * File: collection.php
 * Holds: Holds the collection of all the items currently loaded (to reduce queries)
 * Created: 09.04.14
 * Last updated: 11.04.14
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
    
    public function __construct() {
        // Init array
        $this->arr = array();
    }
    
    //
    // Add Item to collection without validating duplicates
    //
    
    public function add($elm) {
        $this->arr[$elm->getId()] = $elm;

        // Do the create process
        $elm->create();
    }

    //
    // Add Item to collection if it is not already there
    //

    public function addIfDoesNotExist($elm) {
        // Get element id
        $elm_id = $elm->getId();

        // Foreach and check if found
        if (count($this->arr) > 0) {
            foreach ($this->arr as $k => $v) {
                if ($k == $elm_id) {
                    return;
                }
            }
        }

        // Not found, add
        $this->arr[$elm_id] = $elm;

        // Do the create process
        $elm->create();
    }
    
    //
    // Return an item
    //

    public function get($id) {
        // Check if found
        if (!isset($this->arr[$id])) {
            // Not found
            return null;
        }
        else {
            // Return object
            return $this->arr[$id];
        }
    }
}
?>