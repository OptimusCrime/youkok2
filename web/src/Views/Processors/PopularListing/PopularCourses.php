<?php
namespace Youkok\Views\Processors\PopularListing;

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

use Youkok\Enums\MostPopularCourse;
use Youkok\Mappers\ElementsMapper;
use Youkok\Mappers\MostPopularCoursesMapper;
use Youkok\Processors\FrontpageFetchProcessor;
use Youkok\Processors\PopularListing\PopularCoursesProcessor;
use Youkok\Processors\UpdateUserMostPopularProcessor;
use Youkok\Views\Processors\BaseProcessorView;

class PopularCourses extends BaseProcessorView
{
    const DELTA_POST_KEY = 'delta';

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Request $request, Response $response, array $args)
    {
        $output = ElementsMapper::map(
            PopularCoursesProcessor::fromDelta(
                $args['delta'],
                $this->container->get('cache'),
                FrontpageFetchProcessor::PROCESSORS_LIMIT,
                $this->container->get('settings')
            ),
            [
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
        if (isset($request->getParams()[static::DELTA_POST_KEY])
            and strlen($request->getParams()[static::DELTA_POST_KEY]) > 0) {
            $delta = $request->getParams()[static::DELTA_POST_KEY];
        }

        UpdateUserMostPopularProcessor
            ::fromSessionHandler($this->sessionHandler)
            ->withDelta($delta)
            ->withKey('frontpage.most_popular_course')
            ->withEnums(MostPopularCourse::all())
            ->run();

        $output = MostPopularCoursesMapper::map(
            PopularCoursesProcessor::fromDelta(
                $delta,
                $this->container->get('cache'),
                FrontpageFetchProcessor::PROCESSORS_LIMIT,
                $this->container->get('settings')
            ),
            [
                'router' => $this->container->get('router')
            ]
        );

        return $this->output($response, $output);
    }
}
