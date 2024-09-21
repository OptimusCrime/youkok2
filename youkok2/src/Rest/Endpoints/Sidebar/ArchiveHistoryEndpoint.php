<?php
namespace Youkok\Rest\Endpoints\Sidebar;

use Exception;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\ArchiveHistoryService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class ArchiveHistoryEndpoint extends BaseRestEndpoint
{
    private ArchiveHistoryService $archiveHistoryService;
    private Logger $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->archiveHistoryService = $container->get(ArchiveHistoryService::class);
        $this->logger = $container->get('logger');
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        try {
            if (!isset($args['id']) || !is_numeric($args['id'])) {
                throw new InvalidRequestException('Malformed id: ' . $args['id']);
            }

            return $this->outputJson($response, [
                'data' => $this->archiveHistoryService->get((int) $args['id'])
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
