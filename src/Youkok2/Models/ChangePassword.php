<?php
namespace Youkok2\Models;

use Youkok2\Models\Controllers\ChangePasswordController;
use Youkok2\Models\StaticControllers\MessageStaticController;

class ChangePassword extends BaseModel
{

    protected $controller;

    private $id;
    private $user;
    private $hash;
    private $timeout;

    protected $schema = [
        'meta' => [
            'table' => 'changepassword',
            'cacheable' => false,
        ],
        'fields' => [
            'id' => [
                'type' => 'integer',
                'null' => false,
                'db' => true,
                'ignore_insert' => true,
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

    public function __construct($data = null) {
        $this->controller = new ChangePasswordController($this);
        
        if (is_string($data)) {
            $this->controller->createByHash($data);
        }
        elseif (is_array($data)) {
            $this->controller->createByArray($data);
        }
    }

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
}
