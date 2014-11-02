<?php
/*
 * File: collection.php
 * Holds: Holds the collection of all the items currently loaded (to reduce queries)
 * Created: 09.04.14
 * Project: Youkok2
*/

//
// Collection of Items. Made to reduce loading and make caching easier
//

class Collection {
    
    //
    // Variables
    //
    
    private $controller;

    private $arr;
    private $isInited;
    
    //
    // Constructor
    //
    
    public function __construct($controller) {
        // Set references
        $this->controller = &$controller;
        
        // Init array
        $this->arr = array();
        
        // Init collection
        $this->isInited = false;
    }
    
    //
    // Add Item to collection without validating duplicates
    //
    
    public function add($elm) {
        // Check if should init self
        if (!$this->isInited) {
            $this->addInitial();
        }
        
        $this->arr[$elm->getId()] = $elm;
    }

    //
    // Add Item to collection if it is not already there
    //

    public function addIfDoesNotExist($elm) {
        // Check if should init self
        if (!$this->isInited) {
            $this->addInitial();
        }
        
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
    
    //
    // Init the entire list
    //
    
    private function addInitial() {
        // Reset variable
        $this->isInited = true;
        
        // Add root element to collection
        $root_element = new Item($this->controller);
        $root_element->createById(1);
        $this->add($root_element);
    }
}