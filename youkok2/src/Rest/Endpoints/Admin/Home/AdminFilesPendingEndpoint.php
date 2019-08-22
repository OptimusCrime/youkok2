<?php
namespace Youkok\Rest\Endpoints\Admin\Home;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Youkok\Biz\Services\Admin\FileListingService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminFilesPendingEndpoint extends BaseRestEndpoint
{
    /** @var FileListingService */
    private $adminFileListingService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->adminFileListingService = $container->get(FileListingService::class);
    }

    public function get(Request $request, Response $response): Response
    {
        return $this->outputJson($response, [
            'data' => $this->adminFileListingService->getPending()
        ]);
    }
}
