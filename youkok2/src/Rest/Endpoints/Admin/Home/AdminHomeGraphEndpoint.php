<?php
namespace Youkok\Rest\Endpoints\Admin\Home;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Youkok\Biz\Services\Admin\HomeGraphService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminHomeGraphEndpoint extends BaseRestEndpoint
{
    private HomeGraphService $homeGraphService;
    private Logger $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->homeGraphService = $container->get(HomeGraphService::class);
        $this->logger = $container->get('logger');
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            return $this->outputJson($response, [
                'data' => $this->homeGraphService->get()
            ]);
        }
        catch (Exception $ex) {
            $this->logger->error($ex);

            return $this->returnInternalServerError($response);
        }
    }
}
