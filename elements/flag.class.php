<?php
/*
 * File: flag.php
 * Holds: Class for a flag belonging to an item
 * Created: 11.05.14
 * Project: Youkok2
 * 
*/

//
// Holds a flag
//

//
// Holds a Course
//

class Flag {
    
    //
    // Some variables
    //
    
    private $controller;

    private $id;
    private $file;
    private $user;
    private $flagged;
    private $type;
    private $active;
    private $data;
    private $message;
    
    //
    // Constructor
    //
    
    public function __construct($controller) {
    	// Store db reference
    	$this->controller = &$controller;

    	// Set all fields to null first
    	$this->id = null;
    	$this->file = null;
    	$this->user = null;
    	$this->flagged = null;
    	$this->type = null;
    	$this->active = null;
    	$this->data = null;
    	$this->message = null;
    }

    //
    // Setters
    //

    public function setAll($arr) {
    	// Loop all fields in the array
    	foreach ($arr as $k => $v) {
    		// Check that the field exists as a property/attribute in this class
    		if (property_exists('Flag', $k)) {
    			// Set value
    			$this->$k = $v;
    		}
    	}
    }

    public function setId($id) {
    	$this->id = $id;
    }

    public function setFile($file) {
    	$this->file = $file;
    }

    public function setUser($user) {
    	$this->user = $user;
    }

    public function setFlagged($flagged) {
    	$this->flagged = $flagged;
    }

    public function setType($type) {
    	$this->type = $type;
    }

    public function setActive($b) {
    	$this->active = $b;
    }

    public function setData($data) {
    	$this->data = $data;
    }

    public function setMessage($m) {
    	$this->message = $m;
    }

    //
    // Getters
    //

    public function getId() {
    	return $this->id;
    }

    public function getFile() {
    	return $this->file;
    }

    public function getUser() {
    	return $this->user;
    }

    public function getFlagged() {
    	return $this->flagged;
    }

    public function getType() {
    	return $this->type;
    }

    public function isActive() {
    	return $this->active;
    }

    public function getData() {
    	return $this->data;
    }

    public function getMessage() {
    	return $this->message;
    }
}