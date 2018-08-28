<?php
namespace Youkok\Rest\Endpoints\PopularListing;

use Slim\Http\Response;
use Slim\Http\Request;

use Youkok\Enums\MostPopularElement;
use Youkok\Mappers\MostPopularElementsMapper;
use Youkok\Biz\Services\FrontpageService;
use Youkok\Biz\Services\PopularListing\PopularElementsService;
use Youkok\Biz\UpdateUserMostPopularProcessor;
use Youkok\Rest\Endpoints\BaseProcessorView;

class PopularElements extends BaseProcessorView
{
    const DELTA_POST_KEY = 'delta';

    public function get(Request $request, Response $response, array $args)
    {


        $output = MostPopularElementsMapper::map(
            $this->container->get(PopularElementsService::class)->run($args['delta'], FrontpageService::PROCESSORS_LIMIT),
            [
                'router' => $this->container->get('router')
            ]
        );
        return $this->output($response, $output);
    }

    public function update(Request $request, Response $response)
    {
        $delta = null;
        if (isset($request->getParams()[static::DELTA_POST_KEY])
            and strlen($request->getParams()[static::DELTA_POST_KEY]) > 0) {
            $delta = $request->getParams()[static::DELTA_POST_KEY];
        }

        UpdateUserMostPopularProcessor
            ::fromSessionHandler($this->sessionService)
            ->withDelta($delta)
            ->withKey('frontpage.most_popular_element')
            ->withEnums(MostPopularElement::all())
            ->run();

        $output = MostPopularElementsMapper::map(
            PopularElementsService::fromDelta(
                $delta,
                $this->container->get('cache'),
                FrontpageService::PROCESSORS_LIMIT
            ),
            [
                'router' => $this->container->get('router')
            ]
        );

        return $this->output($response, $output);
    }
}
