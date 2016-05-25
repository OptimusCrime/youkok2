<?php
/*
 * File: Wrapper.php
 * Holds: Defines the wrapper that holds the application
 * Created: 29.04.2016
 * Project: Youkok2
 * 
 */

namespace Youkok2\Wrapper;

class Wrapper
{
    
    private $container;
    
    public function __construct($c) {
        // Set reference to wrapper in the container
        $c->setWrapper($this);
        
        // Set the container
        $this->container = $c;
    }
    
    public function run() {
        // Set all sessions
        $sessions = $this->container->getSessions();
        if (count($sessions) > 0) {
            foreach ($sessions as $key => $value) {
                $_SESSION[$key] = $value;
            }
        }

        // Make sure to delete all sessions that are no longer valid
        foreach ($_SESSION as $key => $value) {
            // Check if this key is in the container session pool
            if ($this->container->getSession($key) === null) {
                // Clear this session variable
                unset($_SESSION[$key]);
            }
        }

        // Set all cookies
        $cookies = $this->container->getCookies();
        if (count($cookies) > 0) {
            foreach ($cookies as $key => $value) {
                // Add to array
                $_COOKIE[$key] = $value;

                // Set the cookie
                setcookie($key, $value, (time() + (60 * 60 * 24 * 30)), '/');
            }
        }

        // Make sure to clear all cookies that are no longer valid
        foreach ($_COOKIE as $key => $value) {
            // Check if this key is in the container cookie pool
            if ($this->container->getCookie($key) === null) {
                // Clear this cookie
                setcookie($key, null, time() - (60 * 60 * 24), '/');
            }
        }

        // Set all the headers
        $headers = $this->container->getHeaders();
        if (count($headers) > 0) {
            foreach ($headers as $key => $value) {
                if ($key == 'status') {
                    $this->setResponseCode($value);
                }
                else {
                    header($key . ':' . $value);
                }
            }
        }
        
        // Check if we have a stream to output
        if (count($this->container->getStreams()) > 0) {
            foreach ($this->container->getStreams() as $v) {
                // Simply echo the content of the stream
                echo $v;
            }
        }
        
        // Output the body
        if ($this->container->getBody() !== null and strlen($this->container->getBody()) > 0) {
            echo $this->container->getBody();
        }
    }
    
    private function setResponseCode($code) {
        http_response_code($code);
    }
}
