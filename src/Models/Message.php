<?php
/*
 * File: Message.php
 * Holds: Holds element for a message
 * Created: 10.05.2015
 * Project: Youkok2
*/

namespace Youkok2\Models;

/*
 * Loads other classes
 */

use \Youkok2\Models\Controllers\MessageController as MessageController;
use \Youkok2\Models\StaticControllers\MessageStaticController as MessageStaticController;

/*
 * The Course class
 */

class Message extends BaseModel {

    /*
     * Variables
     */

    protected $controller;

    // Fields in the database
    private $id;
    private $timeStart;
    private $timeEnd;
    private $message;
    private $type;
    private $pattern;

    /*
     * Schema
     */

    protected $schema = [
        'meta' => [
            'table' => 'message',
            'cacheable' => false,
        ],
        'fields' => [
            // Database fields
            'id' => [
                'type' => 'integer',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
            'time_start' => [
                'method' => 'timeStart',
                'type' => 'date',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
            'time_end' => [
                'method' => 'timeEnd',
                'type' => 'date',
                'null' => false,
                'db' => true,
                'arr' => true,
            ],
            'message' => [
                'type' => 'string',
                'null' => true,
                'default' => null,
                'db' => true,
                'arr' => true,
            ],
            'type' => [
                'type' => 'string',
                'null' => true,
                'default' => 'success',
                'db' => true,
                'arr' => true,
            ],
            'pattern' => [
                'type' => 'string',
                'null' => true,
                'default' => '*',
                'db' => true,
                'arr' => true,
            ]
        ]
    ];

    /*
     * Constructor
     */

    public function __construct($data = null) {
        $this->controller = new MessageController($this);

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
    public function getTimeStart() {
        return $this->timeStart;
    }
    public function getTimeEnd() {
        return $this->timeEnd;
    }
    public function getMessage() {
        return $this->message;
    }
    public function getType() {
        return $this->type;
    }
    public function getPattern() {
        return $this->pattern;
    }

    /*
     * Setters
     */

    public function setId($id) {
        $this->id = $id;
    }
    public function setTimeStart($time) {
        $this->timeStart = $time;
    }
    public function setTimeEnd($time) {
        $this->timeEnd = $time;
    }
    public function setMessage($msg) {
        $this->message = $msg;
    }
    public function setType($type) {
        $this->type = $type;
    }
    public function setPattern($pattern) {
        $this->pattern = $pattern;
    }
    
    /*
     * Static functions overload
     */
    
    public static function __callStatic($name, $arguments) {
        // Check if method exists
        if (method_exists('\Youkok2\Models\StaticControllers\MessageStaticController', $name)) {
            // Call method and return response
            return call_user_func_array(array('\Youkok2\Models\StaticControllers\MessageStaticController', 
                $name), $arguments);
        }
    }
} 