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
use Youkok\Biz\Services\Admin\FileDetailsService;
use Youkok\Biz\Services\Admin\FileListingService;
use Youkok\Biz\Services\Admin\FileUpdateService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminFilesEndpoint extends BaseRestEndpoint
{
    private FileListingService $fileListingService;
    private FileDetailsService $fileDetailsService;
    private FileUpdateService $fileUpdateService;
    private Logger $logger;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->fileListingService = $container->get(FileListingService::class);
        $this->fileDetailsService = $container->get(FileDetailsService::class);
        $this->fileUpdateService = $container->get(FileUpdateService::class);
        $this->logger = $container->get('logger');
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        try {
            return $this->outputJson($response, [
                'data' => $this->fileDetailsService->get((int)$args['id'])
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnInternalServerError($response);
        }
    }

    public function list(Request $request, Response $response): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            return $this->outputJson($response, [
                'data' => $this->fileListingService->getAll($routeParser)
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnInternalServerError($response);
        }
    }

    public function listSingle(Request $request, Response $response, array $args): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            return $this->outputJson($response, [
                'data' => $this->fileListingService->getOne($routeParser,
                    (int) $args['id'],
                )
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnInternalServerError($response);
        }
    }

    public function put(Request $request, Response $response, array $args): Response
    {
        try {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();

            $data = $this->getJsonArrayFromBody(
                $request,
                ['course']
            );

            // Remember to run the update service before fetching the updated information below
            $course = $this->fileUpdateService->put(
                $routeParser,
                (int) $data['course'],
                (int) $args['id'],
                $data
            );

            return $this->outputJson($response, [
                'data' => [
                    'element' => $this->fileDetailsService->get((int) $args['id']),
                    'course' => $course
                ]
            ]);
        } catch (InvalidRequestException $ex) {
            $this->logger->debug($ex);
            return $this->returnBadRequest($response);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }
}
