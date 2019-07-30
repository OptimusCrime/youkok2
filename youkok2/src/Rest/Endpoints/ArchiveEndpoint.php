<?php

namespace Youkok\Rest\Endpoints;

use Monolog\Logger;
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

    /** @var Logger */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->archiveService = $container->get(ArchiveService::class);
        $this->elementMapper = $container->get(ElementMapper::class);
        $this->logger = $container->get(Logger::class);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        try {
            if (!isset($args['id']) || !is_numeric($args['id'])) {
                throw new InvalidRequestException('Malformed id: ' . $args['id']);
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
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        } catch (InvalidRequestException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }
}
