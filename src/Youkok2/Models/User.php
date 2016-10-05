<?php
namespace Youkok2\Models;

use Youkok2\Models\Controllers\BaseController;

class User extends BaseModel
{
    
    public $controller;
    
    private $id;
    private $email;
    private $password;
    private $nick;
    private $moduleSettings;
    private $lastSeen;
    private $karma;
    private $karmaPending;
    private $banned;
    
    protected $schema = [
        'meta' => [
            'table' => 'user',
            'cacheable' => false,
        ],
        'fields' => [
            'id' => [
                'type' => 'integer',
                'null' => false,
                'db' => true,
                'arr' => true,
                'ignore_insert' => true,
                'ignore_update' => true,
            ],
            'email' => [
                'type' => 'string',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
            'password' => [
                'type' => 'string',
                'null' => false,
                'db' => true,
            ],
            'nick' => [
                'type' => 'string',
                'null' => true,
                'default' => null,
                'db' => true,
                'arr' => true,
            ],
            'module_settings' => [
                'method' => 'moduleSettings',
                'type' => 'text',
                'null' => true,
                'default' => null,
                'db' => true,
                'arr' => true,
            ],
            'last_seen' => [
                'method' => 'lastSeen',
                'type' => 'datetime',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
                'db' => true,
                'arr' => true,
            ],
            'karma' => [
                'type' => 'integer',
                'null' => false,
                'default' => 5,
                'db' => true,
                'arr' => true,
            ],
            'karma_pending' => [
                'method' => 'karmaPending',
                'type' => 'integer',
                'null' => false,
                'default' => 0,
                'db' => true,
                'arr' => true,
            ],
            'banned' => [
                'type' => 'boolean',
                'null' => false,
                'default' => false,
                'db' => true,
                'arr' => true,
                'is' => true
            ],
        ]
    ];

    public function __construct($data = null) {
        $this->controller = new BaseController(null, $this);

        $this->setDefaults();

        if (is_numeric($data)) {
            $this->controller->createById($data);
        }
        elseif (is_array($data)) {
            $this->controller->createByArray($data);
        }
    }
    
    public function getId() {
        return $this->id;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getPassword() {
        return $this->password;
    }
    public function getNick($raw = true) {
        // Check if we should return the actual database value
        if ($raw) {
            return $this->nick;
        }
        
        // This is used to pretty the nick
        if ($this->nick === null or $this->nick == '') {
            return '<em>Anonym</em>';
        }
        return $this->nick;
    }
    public function getModuleSettings() {
        return $this->moduleSettings;
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
    
    public function setId($id) {
        $this->id = $id;
    }
    public function setEmail($email) {
        $this->email = $email;
    }
    public function setPassword($password) {
        $this->password = $password;
    }
    public function setNick($nick) {
        $this->nick = $nick;
    }
    public function setModuleSettings($settings) {
        $this->moduleSettings = $settings;
    }
    public function setLastSeen($time) {
        $this->lastSeen = $time;
    }
    public function setKarma($karma) {
        $this->karma = $karma;
    }
    public function setKarmaPending($karma) {
        $this->karmaPending = $karma;
    }
    public function setBanned($banned) {
        $this->banned = $banned;
    }
}
