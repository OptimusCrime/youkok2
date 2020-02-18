<?php
namespace Youkok\Rest\Endpoints\Admin\Diagnostics;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Services\Admin\CacheContentService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminRedisCache extends BaseRestEndpoint
{
    /** @var CacheContentService */
    private $cacheContentService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->cacheContentService = $container->get(CacheContentService::class);
    }

    public function get(Request $request, Response $response): Response
    {
        return $this->outputJson($response, [
            'data' => $this->cacheContentService->getAllCacheContent()
        ]);
    }
}
