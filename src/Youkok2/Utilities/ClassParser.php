<?php
/*
 * File: QueryParser.php
 * Holds: Parses the query from the web server engine
 * Created: 01.05.2016
 * Project: Youkok2
 * 
 */
 
namespace Youkok2\Utilities;

class ClassParser {

    private $classPath;
    private $method;
    
    /*
     * Constructor
     */
    
    public function __construct($path, $method = 'run') {
        $this->classPath = '\Youkok2\\' . $path;
        $this->method = $method;
    }
    
    /*
     * Getter for the path
     */
    
    public function getClass() {
        return [
            'view' => $this->classPath,
            'method' => $this->method
        ];
    }
}