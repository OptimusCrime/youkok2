<?php
namespace Youkok\Rest\Endpoints\Admin\Home;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Youkok\Biz\Services\Admin\HomePendingService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class AdminFilesPendingEndpoint extends BaseRestEndpoint
{
    /** @var HomePendingService */
    private $adminHomePendingService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->adminHomePendingService = $container->get(HomePendingService::class);
    }

    public function get(Request $request, Response $response): Response
    {
        return $this->outputJson($response, [
            'data' => [
                $this->adminHomePendingService->get()
            ]
        ]);
    }
}
