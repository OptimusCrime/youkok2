<?php
namespace Youkok\Rest\Endpoints\Sidebar;

use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Enums\MostPopularElement;
use Youkok\Rest\Endpoints\BaseProcessorView;
use Youkok\Biz\Services\Mappers\ElementMapper;

class MostPopular extends BaseProcessorView
{
    const SERVICE_LIMIT = 10;

    /** @var \Youkok\Biz\Services\PopularListing\MostPopularElementsService */
    private $mostPopularElementsService;

    /** @var \Youkok\Biz\Services\Mappers\ElementMapper */
    private $elementMapper;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->mostPopularElementsService = $container->get(MostPopularElementsService::class);
        $this->elementMapper = $container->get(ElementMapper::class);
    }

    public function get(Request $request, Response $response)
    {
        return $this->output($response, [
            'data' => $this->getMostPopularElements()
        ]);
    }

    private function getMostPopularElements()
    {
        $mostPopular = $this->mostPopularElementsService->fromDelta(MostPopularElement::MONTH, static::SERVICE_LIMIT);

        return $this->elementMapper->map(
            $mostPopular, [
                ElementMapper::DATASTORE_DOWNLOADS,
                ElementMapper::PARENT_DIRECT,
                ElementMapper::PARENT_COURSE
            ]
        );
    }
}
