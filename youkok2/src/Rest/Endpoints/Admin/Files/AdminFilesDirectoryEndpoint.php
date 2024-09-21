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
use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\Admin\FileCreateDirectoryService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminFilesDirectoryEndpoint extends BaseRestEndpoint
{
    private FileCreateDirectoryService $fileCreateDirectoryService;
    private Logger $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->fileCreateDirectoryService = $container->get(FileCreateDirectoryService::class);
        $this->logger = $container->get('logger');
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            $data = $this->getJsonArrayFromBody(
                $request,
                [
                    'course',
                    'directory',
                    'value'
                ]
            );

            return $this->outputJson($response, [
                'data' => $this->fileCreateDirectoryService->createDirectory(
                    $routeParser,
                    (int) $data['course'],
                    (int) $data['directory'],
                    (string) $data['value'],
                )
            ]);
        } catch (InvalidRequestException $ex) {
            $this->logger->debug($ex);
            return $this->returnBadRequest($response);
        } catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnBadRequest($response);
        }
    }
}
