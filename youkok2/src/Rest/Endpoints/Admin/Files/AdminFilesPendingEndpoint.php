<?php
namespace Youkok\Rest\Endpoints\Admin\Files;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Slim\Routing\RouteContext;
use Youkok\Biz\Services\Admin\FileListingService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminFilesPendingEndpoint extends BaseRestEndpoint
{
    private FileListingService $fileListingService;
    private Logger $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->fileListingService = $container->get(FileListingService::class);
        $this->logger = $container->get('logger');
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            return $this->outputJson($response, [
                'data' => $this->fileListingService->getPending($routeParser)
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnBadRequest($response);
        }
    }
}
