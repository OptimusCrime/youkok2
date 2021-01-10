<?php
namespace Youkok\Rest\Endpoints\Sidebar\Post;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\Post\TitleFetchService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class TitleFetchEndpoint extends BaseRestEndpoint
{
    private TitleFetchService $titleFetchService;
    private Logger $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->titleFetchService = $container->get(TitleFetchService::class);
        $this->logger = $container->get(Logger::class);
    }

    public function put(Request $request, Response $response): Response
    {
        try {
            $data = $this->getJsonArrayFromBody($request, ['url']);
            return $this->outputJson($response, [
                'title' => $this->titleFetchService->run($data['url'])
            ]);
        } catch (InvalidRequestException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }
}
