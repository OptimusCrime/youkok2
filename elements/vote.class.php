<?php
/*
 * File: vote.php
 * Holds: Class for a vote belonging to a flag
 * Created: 11.05.14
 * Project: Youkok2
 * 
*/

//
// Holds a vote for a flag
//

class Vote {

    //
    // Variables
    //

	// Pointer to the controller
    private $controller;

    // Database fields
    private $id;
    private $user;
    private $flag;
    private $value;
    private $voted;

    public function __construct($controller, $flag) {
    	// Store references
    	$this->controller = &$controller;
    	$this->flag = &$flag;

    	// Set all fields to null first
    	$this->id = null;
    	$this->user = null;
    	$this->value = null;
    	$this->voted = null;
    }

    //
    // Setters
    //

    public function setAll($arr) {
    	$this->id = $arr['id'];
    	$this->user = $arr['user'];
    	$this->value = $arr['value'];
    	$this->voted = $arr['voted'];
    }

    //
    // Getters
    //

    public function getId() {
    	return $this->id;
    }

    public function getUser() {
    	return $this->user;
    }

    public function getFlag() {
    	return $this->flag;
    }

    public function getValue() {
    	return $this->value;
    }

    public function getVoted() {
    	return $this->voted;
    }
}