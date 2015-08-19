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

use \Youkok2\Models\Controllers\MessageController as MessageController;
use \Youkok2\Models\StaticControllers\MessageStaticController as MessageStaticController;

/*
 * The Course class
 */

class ChangePassword extends BaseModel {

    /*
     * Variables
     */

    protected $controller;

    // Fields in the database
    private $id;
    private $user;
    private $hash;
    private $timeout;

    /*
     * Schema
     */

    protected $schema = [
        'meta' => [
            'table' => 'changepassword',
            'cacheable' => false,
        ],
        'fields' => [
            // Database fields
            'id' => [
                'type' => 'integer',
                'null' => false,
                'db' => true,
            ],
            'user' => [
                'type' => 'integer',
                'null' => false,
                'db' => true,
            ],
            'hash' => [
                'type' => 'string',
                'null' => false,
                'db' => true,
            ],
            'timeout' => [
                'type' => 'datetime',
                'null' => false,
                'db' => true,
            ]
        ]
    ];

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