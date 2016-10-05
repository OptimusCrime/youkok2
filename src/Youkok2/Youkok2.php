<?php
namespace Youkok2;

use Youkok2\Utilities\Loader;
use Youkok2\Utilities\Routes;

class Youkok2
{

    protected $wrapper;
    
    private $body;
    private $headers;
    private $streams;
    private $sessions;
    private $cookies;
    private $post;
    private $get;
    private $server;
    private $files; // TODO
    
    public function __construct() {
        $this->body = '';
        $this->headers = [];
        $this->streams = [];
        $this->sessions = [];
        $this->cookies = [];
        $this->post = [];
        $this->get = [];
        $this->server = [];

        // Set default response
        $this->setStatus(200);
    }

    public function setInformation() {
        $this->sessions = $_SESSION;
        $this->cookies = $_COOKIE;
        $this->post = $_POST;
        $this->get = $_GET;
        $this->server = $_SERVER;
    }
    
    public function load($target, $settings = []) {
        $path = null;

        if ($settings === null) {
            $settings = [];
        }
        
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
            
            $class = Loader::getClass($path);
        }

        // If view is null we should instead redirect this request
        if ($class['view'] === null) {
            $this->send($class['redirect'], true, 301);
            return null;
        }
        
        $view = new $class['view']($this);
        
        // Special case handling for processors that are called with URL
        if ($view::isProcessor() and count($settings) === 0) {
            $settings['application'] = true;
            $settings['close_db'] = true;
        }

        if ($view::isProcessor()) {
            $view->setMethod($class['method']);
        }
            
        $view->setSettings($settings);
        $view->setPath($path);

        if ($view::isProcessor()) {
            $view->execute();
        }
        elseif ($class['method'] === null) {
            $view->run();
        }
        else {
            $view->{$class['method']}();
        }

        if ($view->getSetting('overwrite') === null || $view->getSetting('overwrite') === false) {
            // Do not overwrite the current view, simply return the data
            return $view;
        }

        // Overwrite the view, return the newly requested view instead
        return $this->load($view->getSetting('overwrite_target'), $view->getSetting('overwrite_settings'));
    }
    
    public function runProcessor($target, $settings = []) {
        if (gettype($target) === 'string') {
            $target = Routes::PROCESSOR . $target;
        }
        
        return $this->load($target, $settings);
    }
    
    public function send($target, $external = false, $code = null) {
        $this->headers['location'] = (!$external ? URL_RELATIVE : '') . $target;

        // Check if we should set status code
        if ($code !== null) {
            $this->setStatus($code);
        }
    }
    
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

    public function setPost($key, $value) {
        $this->post[$key] = $value;
    }

    public function setGet($key, $value) {
        $this->get[$key] = $value;
    }

    public function setServer($key, $value) {
        $this->server[$key] = $value;
    }
    
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

    public function getPost($key) {
        if (isset($this->post[$key])) {
            return $this->post[$key];
        }
        return null;
    }

    public function getGet($key) {
        if (isset($this->get[$key])) {
            return $this->get[$key];
        }
        return null;
    }

    public function getServer($key) {
        if (isset($this->server[$key])) {
            return $this->server[$key];
        }
        return null;
    }
}
