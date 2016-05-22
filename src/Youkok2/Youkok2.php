<?php
/*
 * File: Youkok2.php
 * Holds: The definite base class for the entire system
 * Created: 01.11.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2;

use Youkok2\Utilities\Loader;
use Youkok2\Utilities\Routes;

class Youkok2 {
    
    protected $wrapper;
    
    private $body;
    private $headers;
    private $streams;
    private $sessions;
    private $cookies;
    
    /*
     * The constructor for the application
     */
    
    public function __construct() {
        // Set initial things
        $this->body = '';
        $this->headers = [];
        $this->streams = [];
        $this->sessions = [];
        $this->cookies = [];

        // Set default response
        $this->setStatus(200);
    }

    /**
     * Load information from cookies and sessions into the wrapper
     */

    public function setInformation() {
        $this->sessions = $_SESSION;
        $this->cookies = $_COOKIE;
    }
    
    /*
     * Loads a view
     */
    
    public function load($target, $settings = []) {
        // Set the default path
        $path = null;
        
        // Check if we are parsing a query or a class
        if (gettype($target) === 'object' and get_class($target) === 'Youkok2\Utilities\ClassParser') {
            // We're using the class parser, simply fetch the class from it
            $class = $target->getClass();
        }
        else {
            // Check if we are parsing a query
            if (gettype($target) === 'object' and get_class($target) === 'Youkok2\Utilities\QueryParser') {
                $path = $target->getPath();
            }
            else {
                // This should be a hard coded URL then
                $path = $target;
            }
            
            // Get the correct view class
            $class = Loader::getClass($path);
        }
        
        // Initiate the view
        $view = new $class['view']($this);
        
        // Special case handling for processors that are called with URL
        if ($view::isProcessor() and count($settings) === 0) {
            $settings['application'] = true;
            $settings['close_db'] = true;
        }

        // Store what method to call
        if ($view::isProcessor()) {
            $view->setMethod($class['method']);
        }        
            
        // Set settings and path
        $view->setSettings($settings);
        $view->setPath($path);
        
        // Check if we should run a specific method or just call the regular handler
        if ($view::isProcessor()) {
            $view->execute();
        }
        else if ($class['method'] === null) {
            $view->run();
        }
        else {
            $view->$class['method']();
        }
        
        return $view;
    }
    
    /*
     * Run a processor with a given action
     */
    
    public function runProcessor($target, $settings = []) {
        // If the processor is a string, be sure to prefix with the processor URL
        if (gettype($target) === 'string') {
            $target = Routes::PROCESSOR . $target;
        }
        
        // Redirect request
        return $this->load($target, $settings);
    }
    
    public function send($target, $external = false) {
        $this->headers['location'] = (!$external ? URL_RELATIVE : '') . $target;
    }
    
    /*
     * Various setters
     */
    
    public function setWrapper($w) {
        $this->wrapper = $w;
    }
    
    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
    }
    
    public function addStream($stream) {
        $this->streams[] = $stream;
    }
    
    public function setStatus($code) {
        $this->headers['status'] = $code;
    }
    
    public function setBody($content) {
        $this->body = $content;
    }

    public function setSession($key, $value) {
        $this->sessions[$key] = $value;
    }

    public function setCookie($key, $value) {
        $this->cookies[$key] = $value;
    }

    public function clearSession($key) {
        if (isset($this->sessions[$key])) {
            unset($this->sessions[$key]);
        }
    }

    public function clearCookie($key) {
        if (isset($this->cookies[$key])) {
            unset($this->cookies[$key]);
        }
    }
    
    /*
     * Various getters
     */
    
    public function getWrapper() {
        return $this->wrapper;
    }
    
    public function getHeader($key) {
        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        }
        return null;
    }
    
    public function getStreams() {
        return $this->streams;
    }
    
    public function getHeaders() {
        return $this->headers;
    }
    
    public function getStatus() {
        return $this->headers['status'];
    }
    
    public function getBody() {
        return $this->body;
    }

    public function getSession($key) {
        if (isset($this->sessions[$key])) {
            return $this->sessions[$key];
        }
        return null;
    }

    public function getCookie($key) {
        if (isset($this->cookies[$key])) {
            return $this->cookies[$key];
        }
        return null;
    }

    public function getSessions() {
        return $this->sessions;
    }

    public function getCookies() {
        return $this->cookies;
    }
}
