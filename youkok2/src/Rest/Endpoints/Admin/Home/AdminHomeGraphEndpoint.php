<?php
namespace Youkok\Rest\Endpoints\Admin\Home;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Services\Admin\HomeGraphService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminHomeGraphEndpoint extends BaseRestEndpoint
{
    /** @var HomeGraphService */
    private $homeGraphService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->homeGraphService = $container->get(HomeGraphService::class);
    }

    public function get(Request $request, Response $response): Response
    {
        return $this->outputJson($response, [
            'data' => $this->homeGraphService->get()
        ]);
    }
}
