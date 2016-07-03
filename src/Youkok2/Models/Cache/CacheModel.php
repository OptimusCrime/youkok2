<?php
/*
 * File: CacheModel.php
 * Holds: Class for all cache models
 * Created: 04.07.2016
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Cache;

use Youkok2\Models\BaseModel;

abstract class CacheModel extends BaseModel
{
    /*
     * Variables
     */

    protected $controllerClass;
    protected $controller;

    // Fields in the database
    private $id;
    private $data;

    /*
     * Schema
     */

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

    /*
     * Constructor
     */

    public function __construct($data = null) {
        $this->controller = new $this->controllerClass($this);

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
    public function getData() {
        return $this->data;
    }

    /*
     * Setters
     */

    public function setId($id) {
        $this->id = $id;
    }
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * Override save
     */
    public function save() {
        $this->cache(true);
    }
}
