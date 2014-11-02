<?php
namespace Youkok2\models;

use Youkok2\models\Element;
use Youkok2\modelcontrollers\File;

class File extends Element {

    /*
     * Constructor
     */

    public function __construct() {
        $this->controller = new \Youkok2\modelcontrollers\File();
    }
} 