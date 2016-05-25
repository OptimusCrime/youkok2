<?php
/*
 * File: MeDownloads.php
 * Holds: Holds data for personal downloads
 * Created: 12.12.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Cache;

use Youkok2\Models\BaseModel;
use Youkok2\Models\Controllers\Cache\MeDownloadsController;

class MeDownloads extends BaseModel
{

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
        $this->controller = new MeDownloadsController($this);

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
    
    /*
     * Static functions overload
     */
    
    public static function __callStatic($name, $arguments) {
        // Check if method exists
        if (method_exists('Youkok2\Models\StaticControllers\Cache\MeDownloadsStaticController', $name)) {
            // Call method and return response
            return call_user_func_array(['Youkok2\Models\StaticControllers\Cache\MeDownloadsStaticController',
                $name], $arguments);
        }
    }
}
