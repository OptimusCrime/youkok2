<?php
namespace Youkok\Rest\Endpoints;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Services\ArchiveService;
use Youkok\Biz\Services\Mappers\ElementMapper;

class Archive extends BaseProcessorView
{
    /** @var \Youkok\Biz\Services\ArchiveService */
    private $archiveService;

    /** @var \Youkok\Biz\Services\Mappers\ElementMapper */
    private $elementMapper;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->archiveService = $container->get(ArchiveService::class);
        $this->elementMapper = $container->get(ElementMapper::class);
    }

    public function get(Request $request, Response $response, array $args)
    {
        $payload = $this->archiveService->get($args['id']);
        $payload['content'] = $this->elementMapper->map($payload['content'], [
            ElementMapper::DOWNLOADS,
            ElementMapper::POSTED_TIME,
            ElementMapper::DOWNLOADS,
            ElementMapper::ICON
        ]);

        return $this->output($response, $payload);
    }
}