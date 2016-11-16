<?php
namespace Youkok2\Models;

use Youkok2\Models\Controllers\BaseController;
use Youkok2\Utilities\Utilities;

class Karma extends BaseModel
{

    protected $controller;
    private $id;
    private $user;
    private $file;
    private $value;
    private $pending;
    private $added;

    protected $schema = [
        'meta' => [
            'table' => 'karma',
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
                'type' => 'boolean',
                'null' => false,
                'default' => true,
                'db' => true,
                'is' => true
            ],
            'state' => [
                'type' => 'boolean',
                'null' => false,
                'default' => true,
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
