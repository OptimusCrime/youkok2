<?php
/*
 * File: Youkok2.php
 * Holds: The definite base class for the entire system
 * Created: 01.11.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2;

use Youkok2\Utilities\Routes;

class Youkok2 {
    
    protected $wrapper;
    
    private $body;
    private $headers;
    private $sessions; // TODO
    private $cookies; // TODO
    
    /*
     * The constructor for the application
     */
    
    public function __construct() {
        // Set initial things
        $this->body = '';
        $this->headers = [];
        
        // Set default response
        $this->setStatus(200);
    }
    
    /*
     * Loads a view
     */
    
    public function load($target, $settings = []) {
        // Set the default path
        $path = null;
        
        // Check if we are parsing a query or a class
        if (get_class($target) === 'Youkok2\Utilities\ClassParser') {
            // We're using the class parser, simply fetch the class from it
            $class = $target->getClass();
        }
        else {
            // Check if we are parsing a query
            if (get_class($target) === 'Youkok2\Utilities\QueryParser') {
                $path = $target->getPath();
            }
            else {
                // This should be a hard coded URL then
                $path = $target;
            }
            
            // Get the correct view class
            $class = Utilities\Loader::getClass($path);
        }
        
        // Initiate the view
        $view = new $class['view']($this);
        $view->setSettings($settings);
        $view->setPath($path);
        
        // Check if we should run a specific method or just call the regular handler
        if ($class['method'] === null) {
            $view->run();
        }
        else {
            $view->$class['method']();
        }
    }
    
    /*
     * Run a processor with a given action TODO
     */
    
    public function runProcessor($action, $settings = []) {
        /*
        // Check if we should return as json
        if (php_sapi_name() != 'cli' and !isset($_GET['format']) and (isset($settings['output']) and $settings['output'])) {
            header('Content-Type: application/json');
        }
        
        // Check override
        if (isset($_GET['format'])) {
            if ($_GET['format'] == 'json') {
                header('Content-Type: application/json');
            }
        }
        
        // Loop the path-array and find what view to load
        $found = false;
        $processor = 'Youkok2\\Processors\\';
        $method = 'run';
        $processors = Routes::getProcessors();
        
        // Loop the routes
        foreach ($processors as $k => $v) {
            foreach ($v as $iv) {
                if ($iv['path'] == $action or substr($iv['path'], 1) == $action) {
                    // We found matching url-pattern, store name
                    $processor .= $k;
                    
                    // Check if we should call a given method
                    if (isset($iv['method'])) {
                        $method = $iv['method'];
                    }
                    
                    // Set to found and break out of the 
                    $found = true;
                    break;
                }
            }
        }
        
        // Check if found
        if (!$found) {
            $processor .= 'NotFound';
        }
        
        // New instance of processor, let the magic happen
        $processor = new $processor($method, $settings);

        // Return the content
        return $processor->getData();
        */
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
    
    public function setStatus($code) {
        $this->headers['status'] = $code;
    }
    
    public function setBody($content) {
        $this->body = $content;
    }
    
    /*
     * Various getters
     */
    
    public function getWrapper() {
        return $this->wrapper = $w;
    }
    
    public function getHeader($key) {
        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        }
        return null;
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
}