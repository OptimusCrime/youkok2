<?php
namespace Youkok2\Wrapper;

class Wrapper
{
    
    private $container;
    
    public function __construct($c) {
        $c->setWrapper($this);
        $this->container = $c;
    }
    
    public function run() {
        $sessions = $this->container->getSessions();
        if (count($sessions) > 0) {
            foreach ($sessions as $key => $value) {
                $_SESSION[$key] = $value;
            }
        }

        foreach ($_SESSION as $key => $value) {
            if ($this->container->getSession($key) === null) {
                unset($_SESSION[$key]);
            }
        }

        $cookies = $this->container->getCookies();
        if (count($cookies) > 0) {
            foreach ($cookies as $key => $value) {
                $_COOKIE[$key] = $value;

                setcookie($key, $value, (time() + (60 * 60 * 24 * 30)), '/');
            }
        }

        foreach ($_COOKIE as $key => $value) {
            if ($this->container->getCookie($key) === null) {
                setcookie($key, null, time() - (60 * 60 * 24), '/');
            }
        }

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
        
        if (count($this->container->getStreams()) > 0) {
            foreach ($this->container->getStreams() as $v) {
                echo $v;
            }
        }
        
        if ($this->container->getBody() !== null and strlen($this->container->getBody()) > 0) {
            echo $this->container->getBody();
        }
    }
    
    private function setResponseCode($code) {
        http_response_code($code);
    }
}
