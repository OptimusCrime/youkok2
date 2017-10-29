<?php
namespace Youkok\Views\Processors\PopularListing;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Enums\MostPopularElement;
use Youkok\Mappers\MostPopularElementsMapper;
use Youkok\Processors\FrontpageFetchProcessor;
use Youkok\Processors\PopularListing\PopularElementsProcessor;
use Youkok\Processors\UpdateUserMostPopularProcessor;
use Youkok\Views\Processors\BaseProcessorView;

class PopularElements extends BaseProcessorView
{
    const DELTA_POST_KEY = 'delta';
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Request $request, Response $response, array $args)
    {
        $output = MostPopularElementsMapper::map(
            PopularElementsProcessor::fromDelta(
                $args['delta'],
                FrontpageFetchProcessor::PROCESSORS_LIMIT,
                $this->container->get('cache')
            ), [
                'router' => $this->container->get('router')
            ]
        );
        return $this->output($response, $output);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function update(Request $request, Response $response)
    {
        $delta = null;
        if (isset($request->getParams()[static::DELTA_POST_KEY]) and strlen($request->getParams()[static::DELTA_POST_KEY]) > 0) {
            $delta = $request->getParams()[static::DELTA_POST_KEY];
        }

        UpdateUserMostPopularProcessor
            ::fromSessionHandler($this->sessionHandler)
            ->withDelta($delta)
            ->withKey('frontpage.most_popular_element')
            ->withEnums(MostPopularElement::all())
            ->run();

        $output = MostPopularElementsMapper::map(
            PopularElementsProcessor::fromDelta(
                $delta,
                FrontpageFetchProcessor::PROCESSORS_LIMIT,
                $this->container->get('cache')
            ), [
                'router' => $this->container->get('router')
            ]
        );

        return $this->output($response, $output);
    }
}
