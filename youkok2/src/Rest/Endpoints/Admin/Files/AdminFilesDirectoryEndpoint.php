<?php
namespace Youkok\Rest\Endpoints\Admin\Files;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

use Youkok\Biz\Exceptions\YoukokException;
use Youkok\Biz\Services\Admin\FileCreateDirectoryService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminFilesDirectoryEndpoint extends BaseRestEndpoint
{
    private FileCreateDirectoryService $adminFileCreateDirectoryService;
    private Logger $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->adminFileCreateDirectoryService = $container->get(FileCreateDirectoryService::class);
        $this->logger = $container->get(Logger::class);
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            $data = $this->getJsonArrayFromBody(
                $request,
                [
                    'course',
                    'directory',
                    'value'
                ]
            );

            return $this->outputJson($response, [
                'data' => $this->adminFileCreateDirectoryService->createDirectory(
                    (int) $data['course'],
                    (int) $data['directory'],
                    (string) $data['value'],
                )
            ]);
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }
}
