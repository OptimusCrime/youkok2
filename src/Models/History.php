<?php
/*
 * File: History.php
 * Holds: Holds a History
 * Created: 24.02.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models;

use \Youkok2\Models\Controllers\HistoryController as HistoryController;

class History {

    /*
     * Variables
     */

    public $controller;

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
        $this->controller = new HistoryController($this);
        
        /*
         * Set some default values
         */

        $this->id = 0;
        $this->user = null;
        $this->file = null;
        $this->historyText = null;
        $this->added = null;
        $this->visible = 1;
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
    public function getHistoryText() {
        return $this->historyText;
    }
    public function getAdded() {
        return $this->added;
    }
    public function isVisible() {
        return (bool) $this->visible;
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
    public function setHistoryText($historyText) {
        $this->historyText = $historyText;
    }
    public function setVisible($visible) {
        $this->visible = $visible;
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