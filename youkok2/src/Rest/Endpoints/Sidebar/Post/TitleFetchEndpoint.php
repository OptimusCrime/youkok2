<?php
namespace Youkok\Rest\Endpoints\Sidebar\Post;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\Post\TitleFetchService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class TitleFetchEndpoint extends BaseRestEndpoint
{
    /** @var TitleFetchService */
    private $titleFetchService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->titleFetchService = $container->get(TitleFetchService::class);
    }

    public function put(Request $request, Response $response)
    {
        try {
            $data = $this->getJsonArrayFromBody($request, ['url']);
            return $this->outputJson($response, [
                'title' => $this->titleFetchService->run($data['url'])
            ]);
        }
        catch (InvalidRequestException $ex) {
            return $this->returnBadRequest($response, $ex);
        }
    }
}
