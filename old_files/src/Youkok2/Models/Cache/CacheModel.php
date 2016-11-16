<?php
namespace Youkok2\Models\Cache;

use Youkok2\Models\BaseModel;

abstract class CacheModel extends BaseModel
{
    
    protected $controllerClass;
    protected $controller;

    private $id;
    private $data;

    protected $schema = [
        'meta' => [
            'db' => false,
            'table' => null,
            'cacheable' => true,
            'queryable' => false
        ],
        'fields' => [
            'id' => [
                'type' => 'integer',
                'null' => false,
                'db' => false,
                'arr' => true,
            ],
            'data' => [
                'type' => 'text',
                'null' => true,
                'db' => false,
                'arr' => true,
            ]
        ]
    ];

    public function __construct($data = null) {
        $this->controller = new $this->controllerClass($this);

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
    
    public function getData() {
        return $this->data;
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function setData($data) {
        $this->data = $data;
    }
    
    public function save() {
        $this->cache(true);
    }
}
