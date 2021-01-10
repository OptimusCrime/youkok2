<?php
namespace Youkok\Rest\Endpoints\Sidebar;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Enums\MostPopularElement;
use Youkok\Rest\Endpoints\BaseRestEndpoint;
use Youkok\Biz\Services\Mappers\ElementMapper;

class MostPopularEndpoint extends BaseRestEndpoint
{
    const SERVICE_LIMIT = 10;

    private MostPopularElementsService $mostPopularElementsService;
    private ElementMapper $elementMapper;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->mostPopularElementsService = $container->get(MostPopularElementsService::class);
        $this->elementMapper = $container->get(ElementMapper::class);
    }

    public function get(Request $request, Response $response): Response
    {
        return $this->outputJson($response, [
            'data' => $this->getMostPopularElements()
        ]);
    }

    private function getMostPopularElements(): array
    {
        $mostPopular = $this->mostPopularElementsService->fromDelta(MostPopularElement::WEEK, static::SERVICE_LIMIT);

        return $this->elementMapper->mapFromArray(
            $mostPopular,
            [
                ElementMapper::DATASTORE_DOWNLOADS,
                ElementMapper::PARENT_DIRECT,
                ElementMapper::PARENT_COURSE
            ]
        );
    }
}
