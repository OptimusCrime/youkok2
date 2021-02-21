<?php
namespace Youkok\Rest\Endpoints\Admin;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Services\Models\ElementService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminPendingNumEndpoint extends BaseRestEndpoint
{
    private ElementService $elementService;

    public function __construct(ContainerInterface $container)
    {
        $this->elementService = $container->get(ElementService::class);
    }

    public function get(Request $request, Response $response): Response
    {
        return $this->outputJson($response, [
            'num' => $this->elementService->getAllPending(),
        ]);
    }
}
