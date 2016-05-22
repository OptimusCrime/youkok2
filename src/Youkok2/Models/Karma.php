<?php
/*
 * File: Karma.php
 * Holds: Holds a Karma
 * Created: 24.02.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models;

use Youkok2\Models\Controllers\KarmaController;
use Youkok2\Utilities\Utilities;

class Karma extends BaseModel 
{

    /*
     * Variables
     */

    protected $controller;

    // Fields in the database
    private $id;
    private $user;
    private $file;
    private $value;
    private $pending;
    private $added;
    
    /*
     * Schema
     */

    protected $schema = [
        'meta' => [
            'table' => 'karma',
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
                'null' => false,
                'db' => true,
            ],
            'file' => [
                'type' => 'integer',
                'null' => false,
                'db' => true,
            ],
            'value' => [
                'type' => 'integer',
                'null' => false,
                'default' => 5,
                'db' => true,
            ],
            'pending' => [
                'type' => 'integer',
                'null' => false,
                'default' => 1,
                'db' => true,
                'is' => true
            ],
            'state' => [
                'type' => 'integer',
                'null' => false,
                'default' => 1,
                'db' => true,
            ],
            'added' => [
                'type' => 'integer',
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
            
        ]
    ];
    
    /*
     * Constructor
     */
    
    public function __construct($data = null) {
        $this->controller = new KarmaController($this);
        
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
    public function getFile($object = false) {
        return $object ? Element::get($this->file) : $this->file;
    }
    public function getValue() {
        return $this->value;
    }
    public function isPending() {
        return $this->pending;
    }
    public function getState() {
        return $this->state;
    }
    public function getAdded($pretty = false) {
        return $pretty ? Utilities::prettifySQLDate($this->added) : $this->added;
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
    public function setState($state) {
        $this->state = $state;
    }
    public function setAdded($added) {
        $this->added = $added;
    }
} 
