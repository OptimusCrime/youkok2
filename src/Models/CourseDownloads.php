<?php
/*
 * File: CourseDownloads.php
 * Holds: Holds data for a course download count
 * Created: 29.11.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models;

use \Youkok2\Models\Controllers\CourseDownloadsController as CourseDownloadsController;

class CourseDownloads extends BaseModel {

    /*
     * Variables
     */

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
        ],
        'fields' => [
            // Database fields
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
        $this->controller = new CourseDownloadsController($this);

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
} 