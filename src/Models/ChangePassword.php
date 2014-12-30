<?php
/*
 * File: ChangePassword.php
 * Holds: Holds element for changing a password
 * Created: 30.12.2014
 * Project: Youkok2
*/

namespace Youkok2\models;

/*
 * Loads other classes
 */

use \Youkok2\Models\Controllers\ChangePasswordController as ChangePasswordController;

/*
 * The Course class
 */

class ChangePassword {

    /*
     * Variables
     */

    private $id;
    private $user;
    private $hash;
    private $timeout;

    /*
     * Constructor
     */

    public function __construct() {
        $this->controller = new ChangePasswordController($this);
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
    public function getHash() {
        return $this->hash;
    }
    public function getTimeout() {
        return $this->timeout;
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
    public function setHash($hash) {
        $this->hash = $hash;
    }
    public function setTimeout($timeout) {
        $this->timeout = $timeout;
    }

    /*
     * Redirectors
     */

    public function createByHash($hash) {
        $this->controller->createByHash($hash);
    }
    public function save() {
        $this->controller->save();
    }
    public function update() {
        $this->controller->update();
    }
    public function delete() {
        $this->controller->delete();
    }
} 