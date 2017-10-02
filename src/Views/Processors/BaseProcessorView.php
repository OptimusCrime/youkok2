<?php
namespace Youkok\Views\Processors;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Views\BaseView;

class BaseProcessorView extends BaseView
{
    protected function output(Response $response, array $object)
    {
        $this->sessionHandler->store(true);

        return $response->withHeader('Content-Type', 'application/json')->withJson($object);
    }
}
