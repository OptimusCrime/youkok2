<?php
/*
 * File: Karma.php
 * Holds: Holds a Karma
 * Created: 24.02.15
 * Project: Youkok2
*/

namespace Youkok2\Models;

/*
 * Load different classes into namespace
 */

use \Youkok2\Models\Controllers\KarmaController as KarmaController;

/*
 * Model for a Element
 */

class Karma {

    /*
     * Variables
     */

    protected $controller;

    // Fields in the database
    private $id;
    private $user;
    private $file;
    private $historyText;
    private $added;
    private $visible;
    
    /*
     * Constructor
     */
    
    public function __construct() {
        $this->controller = new KarmaController($this);
        
        /*
         * Set some default values
         */

        $this->id = 0;
        $this->user = null;
        $this->file = null;
        $this->value = 5;
        $this->pending = true;
        $this->added = null;
    }
    
    /*
     * Getters
     */

    public function getId() {
        return $this->id;
    }
    public function getUser() {
        return $this->user;
    }
    public function getFile() {
        return $this->file;
    }
    public function getValue() {
        return $this->value;
    }
    public function isPending() {
        return (bool) $this->pending;
    }
    public function getAdded() {
        return $this->added;
    }
    
    /*
     * Setters
     */

    public function setId($id) {
        $this->id = $id;
    }
    public function setUser($user) {
        $this->user = $user;
    }
    public function setFile($file) {
        $this->file = $file;
    }
    public function setValue($value) {
        $this->value = $value;
    }
    public function setPending($pending) {
        $this->pending = $pending;
    }
    
    /*
     * Redirectors
     */
    
    public function save() {
        $this->controller->save();
    }
    public function update() {
        $this->controller->update();
    }

} 