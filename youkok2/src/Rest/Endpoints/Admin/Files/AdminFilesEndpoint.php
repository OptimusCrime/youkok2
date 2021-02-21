<?php
namespace Youkok\Rest\Endpoints\Admin\Files;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

use Youkok\Biz\Exceptions\YoukokException;
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

    public function __construct(ContainerInterface $container)
    {
        $this->fileListingService = $container->get(FileListingService::class);
        $this->fileDetailsService = $container->get(FileDetailsService::class);
        $this->fileUpdateService = $container->get(FileUpdateService::class);
        $this->logger = $container->get(Logger::class);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        try {
            return $this->outputJson($response, [
                'data' => $this->fileDetailsService->get((int)$args['id'])
            ]);
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }

    public function list(Request $request, Response $response): Response
    {
        try {
            return $this->outputJson($response, [
                'data' => $this->fileListingService->getAll()
            ]);
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }

    public function listSingle(Request $request, Response $response, array $args): Response
    {
        try {
            return $this->outputJson($response, [
                'data' => $this->fileListingService->get((int)$args['id'])
            ]);
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }

    public function put(Request $request, Response $response, array $args): Response
    {
        try {
            $data = $this->getJsonArrayFromBody(
                $request,
                ['course']
            );

            // Remember to run the update service before fetching the updated information below
            $course = $this->fileUpdateService->put(
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
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }
}
