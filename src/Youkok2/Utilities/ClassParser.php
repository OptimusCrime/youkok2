<?php
namespace Youkok2\Utilities;

class ClassParser
{

    private $classPath;
    private $method;
    
    public function __construct($path, $method = 'run') {
        $this->classPath = '\Youkok2\\' . $path;
        $this->method = $method;
    }
    
    public function getClass() {
        return [
            'view' => $this->classPath,
            'method' => $this->method
        ];
    }
}
