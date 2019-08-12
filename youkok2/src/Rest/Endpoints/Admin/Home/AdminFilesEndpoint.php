<?php
namespace Youkok\Rest\Endpoints\Admin\Home;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Youkok\Biz\Services\Admin\FileDetailsService;
use Youkok\Biz\Services\Admin\FileListingService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminFilesEndpoint extends BaseRestEndpoint
{
    /** @var FileListingService */
    private $adminFileListingService;

    /** @var FileDetailsService */
    private $adminFileDetailsService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->adminFileListingService = $container->get(FileListingService::class);
        $this->adminFileDetailsService = $container->get(FileDetailsService::class);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        return $this->outputJson($response, [
            'data' => $this->adminFileDetailsService->get((int) $args['id'])
        ]);
    }

    public function list(Request $request, Response $response): Response
    {
        return $this->outputJson($response, [
            'data' => $this->adminFileListingService->getAll()
        ]);
    }


}
