<?php
namespace Youkok\Rest\Endpoints\Sidebar\Post\Create;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\Post\Create\CreateLinkService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class CreateLinkEndpoint extends BaseRestEndpoint
{
    private CreateLinkService $createLinkService;
    private Logger $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->createLinkService = $container->get(CreateLinkService::class);
        $this->logger = $container->get('logger');
    }

    public function put(Request $request, Response $response): Response
    {
        try {
            $data = $this->getJsonArrayFromBody(
                $request,
                [
                    'url',
                    'title',
                    'id'
                ]
            );

            $this->createLinkService->run(
                $data['id'],
                $data['url'],
                $data['title']
            );

            return $this->outputSuccess($response);
        } catch (InvalidRequestException $ex) {
            $this->logger->debug($ex);
            return $this->returnBadRequest($response);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }
}
