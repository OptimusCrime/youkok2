<?php
namespace Youkok\Rest\Endpoints\Admin\Files;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

use Youkok\Biz\Exceptions\YoukokException;
use Youkok\Biz\Services\Admin\FileListingService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminFilesPendingEndpoint extends BaseRestEndpoint
{
    private FileListingService $adminFileListingService;
    private Logger $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->adminFileListingService = $container->get(FileListingService::class);
        $this->logger = $container->get(Logger::class);
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            return $this->outputJson($response, [
                'data' => $this->adminFileListingService->getPending()
            ]);
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }
}
