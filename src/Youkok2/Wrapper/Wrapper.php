<?php
/*
 * File: Wrapper.php
 * Holds: Defines the wrapper that holds the application
 * Created: 29.04.2016
 * Project: Youkok2
 * 
 */

namespace Youkok2\Wrapper;

class Wrapper {
    
    private $container;
    
    public function __construct($c) {
        // Set reference to wrapper in the container
        $c->setWrapper($this);
        
        // Set the container
        $this->container = $c;
    }
    
    public function run() {
        // Loop and set all the headers
        foreach ($this->container->getHeaders() as $key => $value) {
            if ($key == 'status') {
                $this->setResponseCode($value);
            }
            else {
                header($key . ':' . $value);
            }
        }
        
        // Output the body
        if ($this->container->getBody() !== null and strlen($this->container->getBody()) > 0) {
            echo $this->container->getBody();
        }
    }
    
    public function debug() {
        echo 'Debug';
        var_dump($this->container->getHeaders());
        
        echo '<pre>';
        echo $this->container->getBody();
        echo '</pre>';
    }
    
    private function setResponseCode($code) {
        http_response_code($code);
    }
}