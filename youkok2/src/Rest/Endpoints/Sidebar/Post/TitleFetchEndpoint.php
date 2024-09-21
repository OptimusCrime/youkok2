<?php
namespace Youkok\Rest\Endpoints\Sidebar\Post;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\Post\TitleFetchService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class TitleFetchEndpoint extends BaseRestEndpoint
{
    private TitleFetchService $titleFetchService;
    private Logger $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->titleFetchService = $container->get(TitleFetchService::class);
        $this->logger = $container->get('logger');
    }

    public function put(Request $request, Response $response): Response
    {
        try {
            $data = $this->getJsonArrayFromBody($request, ['url']);
            return $this->outputJson($response, [
                'title' => $this->titleFetchService->run($data['url'])
            ]);
        } catch (InvalidRequestException $ex) {
            $this->logger->debug($ex);
            return $this->returnBadRequest($response);
        } catch (Exception $ex) {
            $this->logger->error($ex);
            return $this->returnInternalServerError($response);
        }
    }
}
