<?php
namespace Youkok\Rest\Endpoints\Sidebar;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Exceptions\YoukokException;
use Youkok\Biz\Services\ArchiveHistoryService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class ArchiveHistoryEndpoint extends BaseRestEndpoint
{
    private ArchiveHistoryService $archiveHistoryService;
    private Logger $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->archiveHistoryService = $container->get(ArchiveHistoryService::class);
        $this->logger = $container->get(Logger::class);
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
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }
}
