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
    private $body;
    private $headers;
    
    public function __construct($c) {
        // Set initial things
        $this->body = '';
        $this->headers = [];
        
        // Set default response
        $this->setStatus(200);
        
        // Set reference to wrapper in the container
        $c->setWrapper($this);
        
        // Set the container
        $this->container = $c;
    }
    
    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
    }
    
    public function setStatus($code) {
        $this->headers['status'] = $code;
    }
    
    public function setBody($content) {
        $this->body = $content;
    }
    
    public function run() {
        // Loop and set all the headers
        foreach ($this->headers as $key => $value) {
            if ($key == 'status') {
                $this->setResponseCode($value);
            }
            else {
                header($key . ':' . $value);
            }
        }
        
        // Output the body
        if ($this->body !== null and strlen($this->body) > 0) {
            echo $this->body;
        }
    }
    
    public function debug() {
        var_dump($this->headers);
        
        echo '<pre>';
        echo $this->body;
        echo '</pre>';
    }
    
    private function setResponseCode($code) {
        http_response_code($code);
    }
}