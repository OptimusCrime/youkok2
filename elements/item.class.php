<?php
/*
 * File: item.php
 * Holds: Class for either a directory or file in the system
 * Created: 09.04.14
 * Last updated: 10.04.14
 * Project: Youkok2
 * 
*/

//
// Either directory or file in the system
//

class Item {
    
    //
    // Variables for the class
    //
    
    const WEEK = 0;
    const MONTH = 1;
    const YEAR = 2;
    const ALL = 3;
    
    private $collection;
    private $id;
    private $url;
    private $name;

    //
    // Constructor
    //
    
    public function _construct(&$collection) {
        $this->collection = $collection;
    }
    
    //
    // Create methods
    //
    
    public function createById($id) {
        $this->id = $id;
    }
    
    public function createByUrl($url) {
        $this->url = $url;
    }

    //
    // Getters
    //

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    //
    // Generate url for this item
    //

    public function generateUrl($path) {
        //
    }

    //
    // Download methods
    //
    
    public function getDownloadCount($delta) {
        //
    }
    
    public function setDownloadCount($delta, $value) {
        //
    }
}
?>