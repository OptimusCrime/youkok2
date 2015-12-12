<?php
/*
 * File: History.php
 * Holds: Holds a History
 * Created: 24.02.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models;

use Youkok2\Models\Controllers\HistoryController;

class History extends BaseModel {

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
     * Schema
     */

    protected $schema = [
        'meta' => [
            'table' => 'history',
            'cacheable' => false,
        ],
        'fields' => [
            // Database fields
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
                'default' => 'NOW()',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
            'visible' => [
                'method' => 'visible',
                'type' => 'integer',
                'null' => false,
                'default' => 1,
                'db' => true,
                'is' => true
            ],
        ]
    ];
    
    /*
     * Constructor
     */
    
    public function __construct($data = null) {
        $this->controller = new HistoryController($this);
        
        /*
         * Set some default values
         */

        $this->setDefaults();
        
        /*
         * Various create methods are called here
         */
        
        if (is_numeric($data)) {
            $this->controller->createById($data);
        }
        elseif (is_array($data)) {
            $this->controller->createByArray($data);
        }
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
    public function setAdded($added) {
        $this->added = $added;
    }
    public function setVisible($visible) {
        $this->visible = $visible;
    }
} 