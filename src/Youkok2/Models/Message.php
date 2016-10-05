<?php
namespace Youkok2\Models;

use Youkok2\Models\Controllers\BaseController;
use Youkok2\Models\StaticControllers\MessageStaticController;

class Message extends BaseModel
{

    protected $controller;

    private $id;
    private $timeStart;
    private $timeEnd;
    private $message;
    private $type;
    private $pattern;

    protected $schema = [
        'meta' => [
            'table' => 'message',
            'cacheable' => false,
        ],
        'fields' => [
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
    
    public static function __callStatic($name, $arguments) {
        if (method_exists('Youkok2\Models\StaticControllers\MessageStaticController', $name)) {
            return call_user_func_array(['Youkok2\Models\StaticControllers\MessageStaticController',
                $name], $arguments);
        }
    }
}
