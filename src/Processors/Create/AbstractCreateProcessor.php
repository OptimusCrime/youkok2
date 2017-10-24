<?php
namespace Youkok\Processors\Create;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

abstract class AbstractCreateProcessor
{
    protected $response;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function withResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    abstract public function run();
}