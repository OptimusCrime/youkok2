<?php
namespace Youkok\Rest\Endpoints\Admin\Diagnostics;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Youkok\Biz\Services\Admin\CacheContentService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminRedisCache extends BaseRestEndpoint
{
    private CacheContentService $cacheContentService;
    private Logger $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->cacheContentService = $container->get(CacheContentService::class);
        $this->logger = $container->get('logger');
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            return $this->outputJson($response, [
                'data' => $this->cacheContentService->getAllCacheContent()
            ]);
        }
        catch (Exception $e) {
            $this->logger->error($e);

            return $this->returnInternalServerError($response);
        }
    }
}
