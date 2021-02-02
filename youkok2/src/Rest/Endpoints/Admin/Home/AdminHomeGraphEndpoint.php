<?php
namespace Youkok\Rest\Endpoints\Admin\Home;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Services\Admin\HomeGraphService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminHomeGraphEndpoint extends BaseRestEndpoint
{
    private HomeGraphService $homeGraphService;

    public function __construct(ContainerInterface $container)
    {
        $this->homeGraphService = $container->get(HomeGraphService::class);
    }

    public function get(Request $request, Response $response): Response
    {
        return $this->outputJson($response, [
            'data' => $this->homeGraphService->get()
        ]);
    }
}
