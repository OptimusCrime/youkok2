<?php
namespace Youkok\Rest\Endpoints;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Youkok\Biz\Exceptions\InvalidRequestException;

class BaseRestEndpoint
{
    protected function outputJson(Response $response, array $object): Response
    {
        $response->getBody()->write(json_encode($object));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * @throws InvalidRequestException
     */
    protected function getJsonArrayFromBody(Request $request, array $ensureKeysExists = []): array
    {
        $body = (string) $request->getBody();
        if (mb_strlen($body) === 0) {
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

    protected function returnBadRequest(Response $response): Response
    {
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'application/json');
    }

    protected function returnInternalServerError(Response $response): Response
    {
        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
    }
}
