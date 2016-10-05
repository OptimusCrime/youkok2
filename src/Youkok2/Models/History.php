<?php
namespace Youkok2\Models;

use Youkok2\Models\Controllers\BaseController;

class History extends BaseModel
{

    protected $controller;
    private $id;
    private $user;
    private $file;
    private $historyText;
    private $added;
    private $visible;
    protected $schema = [
        'meta' => [
            'table' => 'history',
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
                'null' => true,
                'default' => null,
                'db' => true,
            ],
            'file' => [
                'type' => 'integer',
                'null' => false,
                'db' => true,
            ],
            'history_text' => [
                'method' => 'historyText',
                'type' => 'string',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
            'added' => [
                'type' => 'integer',
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
            'visible' => [
                'type' => 'boolean',
                'null' => false,
                'default' => true,
                'db' => true,
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
    public function setAdded($added) {
        $this->added = $added;
    }
    public function setVisible($visible) {
        $this->visible = $visible;
    }
}
