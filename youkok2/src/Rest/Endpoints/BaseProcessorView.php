<?php
namespace Youkok\Rest\Endpoints;

use Slim\Http\Response;

use Youkok\Web\Views\BaseView;

class BaseProcessorView extends BaseView
{
    protected function output(Response $response, array $object)
    {
        $this->sessionService->store(true);

        return $response->withHeader('Content-Type', 'application/json')->withJson($object);
    }

    protected function outputEmpty(Response $response) {
        return $response->withHeader('Content-Type', 'application/json');
    }
}