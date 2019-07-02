<?php
namespace Youkok\Rest\Endpoints\Sidebar;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\ArchiveHistoryService;
use Youkok\Rest\Endpoints\BaseRestEndpoint;

class ArchiveHistoryEndpoint extends BaseRestEndpoint
{
    /** @var ArchiveHistoryService */
    private $archiveHistoryService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->archiveHistoryService = $container->get(ArchiveHistoryService::class);
    }

    public function get(Request $request, Response $response, array $args)
    {
        try {
            if (!isset($args['id']) || !is_numeric($args['id'])) {
                return $this->returnBadRequest($response, new InvalidRequestException('Malformed id: ' . $args['id']));
            }

            return $this->outputJson($response, [
                'data' => $this->archiveHistoryService->get((int) $args['id'])
            ]);
        } catch (ElementNotFoundException $ex) {
            return $this->returnBadRequest($response, $ex);
        }
    }
}
