<?php
namespace Youkok\Rest\Endpoints\Admin;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Youkok\Biz\Services\Models\ElementService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminPendingNumEndpoint extends BaseRestEndpoint
{
    private ElementService $elementService;
    private Logger $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->elementService = $container->get(ElementService::class);
        $this->logger = $container->get('logger');
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            return $this->outputJson($response, [
                'num' => $this->elementService->getAllPending(),
            ]);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response);
        }

    }
}
