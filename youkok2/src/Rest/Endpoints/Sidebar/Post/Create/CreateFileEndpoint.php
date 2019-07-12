<?php
namespace Youkok\Rest\Endpoints\Sidebar\Post\Create;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Youkok\Biz\Services\Post\Create\CreateLinkService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class CreateFileEndpoint extends BaseRestEndpoint
{
    /** @var CreateLinkService */
    private $createLinkService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->createLinkService = $container->get(CreateLinkService::class);
    }

    public function put(Request $request, Response $response)
    {
        return $this->outputJson($response, ['ok']);
    }
}
