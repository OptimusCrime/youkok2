<?php
namespace Youkok\Rest\Endpoints;

use Slim\Http\Response;

use Youkok\Web\Views\BaseView;

class BaseRestEndpoint extends BaseView
{
    protected function outputJson(Response $response, array $object): Response
    {
        $this->sessionService->store();

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withJson($object);
    }

    protected function returnBadRequest(Response $response, \Exception $ex): Response
    {
        if (getenv('DEV') === '1') {
            return $response->write($ex->getTraceAsString());
        }

        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'application/json');
    }
}
