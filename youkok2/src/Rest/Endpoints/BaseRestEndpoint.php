<?php
namespace Youkok\Rest\Endpoints;

use Slim\Http\Request;
use Slim\Http\Response;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Helpers\Configuration\Configuration;
use Youkok\Web\Views\BaseView;

class BaseRestEndpoint extends BaseView
{
    protected function outputJson(Response $response, array $object): Response
    {
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withJson($object);
    }

    protected function getJsonArrayFromBody(Request $request, array $ensureKeysExists = []): array
    {
        $body = (string) $request->getBody();
        if ($body === null || mb_strlen($body) === 0) {
            throw new InvalidRequestException('Malformed request');
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            throw new InvalidRequestException('Malformed request');
        }

        foreach ($ensureKeysExists as $key) {
            if (!isset($data[$key])) {
                throw new InvalidRequestException('Malformed request');
            }
        }

        return $data;
    }

    protected function outputSuccess(Response $response): Response
    {
        return $this->outputJson($response, ['state' => 'OK']);
    }

    protected function returnBadRequest(Response $response, \Exception $ex): Response
    {
        if (Configuration::getInstance()->isDev()) {
            return $response->write($ex->getTraceAsString());
        }

        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'application/json');
    }
}
