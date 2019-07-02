<?php

namespace Youkok\Rest\Endpoints;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\InvalidRequestException;
use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\Mappers\ElementMapper;

class ArchiveEndpoint extends BaseRestEndpoint
{
    /** @var ArchiveService */
    private $archiveService;

    /** @var ElementMapper */
    private $elementMapper;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->archiveService = $container->get(ArchiveService::class);
        $this->elementMapper = $container->get(ElementMapper::class);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        try {
            if (!isset($args['id']) || !is_numeric($args['id'])) {
                return $this->returnBadRequest($response, new InvalidRequestException('Malformed id: ' . $args['id']));
            }

            return $this->outputJson(
                $response,
                $this->elementMapper->map(
                    $this->archiveService->get((int) $args['id']),
                    [
                        ElementMapper::DOWNLOADS,
                        ElementMapper::POSTED_TIME,
                        ElementMapper::DOWNLOADS,
                        ElementMapper::ICON
                    ]
                )
            );
        } catch (ElementNotFoundException $ex) {
            return $this->returnBadRequest($response, $ex);
        }
    }
}
