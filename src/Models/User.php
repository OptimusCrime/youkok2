<?php
/*
 * File: User.php
 * Holds: Class for a user
 * Created: 06.11.2014
 * Project: Youkok2
 *
 */

namespace Youkok2\models;

use \Youkok2\models\models\UserController as UserController;

class User {
    
    /*
     * Variables
     */
    
    public $controller;
    
    private $id;
    private $email;
    private $password;
    private $salt;
    private $nick;
    private $mostPopularDelta;
    private $lastSeen;
    private $karma;
    private $karmaPending;
    private $banned;
    
    /*
     * Constructor
     */
    
    public function __construct() {
        $this->controller = new UserController($this);
    }
    
    /*
     * Getters
     */
    
    public function getId() {
        return $this->id;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getPassword() {
        return $this->password;
    }
    public function getSalt() {
        return $this->salt;
    }
    public function getNick() {
        return $this->nick;
    }
    public function getMostPopularDelta() {
        return $this->mostPopularDelta;
    }
    public function getLastSeen() {
        return $this->lastSeen;
    }
    public function getKarma() {
        return $this->karma;
    }
    public function getKarmaPending() {
        return $this->karmaPending;
    }
    public function isBanned() {
        return $this->banned;
    }
}