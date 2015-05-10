<?php
/*
 * File: Message.php
 * Holds: Holds element for a message
 * Created: 10.05.2015
 * Project: Youkok2
*/

namespace Youkok2\models;

/*
 * Loads other classes
 */

use \Youkok2\Models\StaticControllers\MessageStaticController as MessageStaticController;

/*
 * The Course class
 */

class Message {

    /*
     * Variables
     */

    private $id;
    private $time_start;
    private $time_end;
    private $message;
    private $type;
    private $pattern;

    /*
     * Constructor
     */

    public function __construct($id, $timeStart, $timeEnd, $message, $type, $pattern) {
        $this->id = $id;
        $this->timeStart = $timeStart;
        $this->timeEnd = $timeEnd;
        $this->message = $message;
        $this->type = $type;
        $this->pattern = $pattern;
    }

    /*
     * Getters
     */

    public function getId() {
        return $this->id;
    }
    public function getTimeStart() {
        return $this->time_start;
    }
    public function getTimeEnd() {
        return $this->time_end;
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