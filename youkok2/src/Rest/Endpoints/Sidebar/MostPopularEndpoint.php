<?php
namespace Youkok\Rest\Endpoints\Sidebar;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Biz\Exceptions\ElementNotFoundException;
use Youkok\Biz\Exceptions\GenericYoukokException;
use Youkok\Biz\Exceptions\InvalidFlagCombination;
use Youkok\Biz\Exceptions\YoukokException;
use Youkok\Biz\Services\PopularListing\MostPopularElementsService;
use Youkok\Enums\MostPopularElement;
use Youkok\Rest\Endpoints\BaseRestEndpoint;
use Youkok\Biz\Services\Mappers\ElementMapper;

class MostPopularEndpoint extends BaseRestEndpoint
{
    const SERVICE_LIMIT = 10;

    private MostPopularElementsService $mostPopularElementsService;
    private ElementMapper $elementMapper;
    private Logger $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->mostPopularElementsService = $container->get(MostPopularElementsService::class);
        $this->elementMapper = $container->get(ElementMapper::class);
        $this->logger = $container->get(Logger::class);
    }

    public function get(Request $request, Response $response): Response
    {
        try {
            return $this->outputJson($response, [
                'data' => $this->getMostPopularElements()
            ]);
        } catch (YoukokException $ex) {
            $this->logger->error($ex);
            return $this->returnBadRequest($response, $ex);
        }
    }

    /**
     * @return array
     * @throws ElementNotFoundException
     * @throws GenericYoukokException
     * @throws InvalidFlagCombination
     */
    private function getMostPopularElements(): array
    {
        $mostPopular = $this->mostPopularElementsService->fromDelta(MostPopularElement::WEEK(), static::SERVICE_LIMIT);

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
