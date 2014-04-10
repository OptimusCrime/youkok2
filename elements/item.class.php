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

    //
    // Constructor
    //
    
    public function _construct(&$collection) {
        $this->collection = $collection;
    }
    
    //
    // Create
    //
    
    public function createById($id) {
        //
    }
    
    public function createByUrl($url) {
        //
    }
    
    public function getDownloadCount($delta) {
        //
    }
    
    public function setDownloadCount($delta, $value) {
        //
    }
}
?>